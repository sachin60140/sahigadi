declare global {
    interface Window {
        PhonePeCheckout?: {
            transact: (options: { tokenUrl: string }) => void;
        };
    }
}

type CheckoutPayload = Record<string, string | number | null | undefined>;

let checkoutScriptPromise: Promise<void> | null = null;

const loadCheckoutScript = () => {
    if (window.PhonePeCheckout?.transact) {
        return Promise.resolve();
    }

    if (checkoutScriptPromise) {
        return checkoutScriptPromise;
    }

    checkoutScriptPromise = new Promise<void>((resolve, reject) => {
        const existing = document.querySelector<HTMLScriptElement>('script[data-phonepe-checkout]');
        if (existing) {
            existing.addEventListener('load', () => resolve(), { once: true });
            existing.addEventListener('error', () => reject(new Error('PhonePe checkout could not be loaded.')), { once: true });
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://mercury.phonepe.com/web/bundle/checkout.js';
        script.async = true;
        script.dataset.phonepeCheckout = 'true';
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('PhonePe checkout could not be loaded.'));
        document.head.appendChild(script);
    });

    return checkoutScriptPromise;
};

const responseMessage = (payload: unknown, fallback: string) => {
    if (payload && typeof payload === 'object') {
        const message = (payload as { message?: unknown }).message;
        if (typeof message === 'string' && message.trim() !== '') {
            return message;
        }

        const errors = (payload as { errors?: Record<string, string[]> }).errors;
        const firstError = errors ? Object.values(errors).flat().find(Boolean) : null;
        if (firstError) {
            return firstError;
        }
    }

    return fallback;
};

export const startPhonePeCheckout = async (
    endpoint: string,
    payload: CheckoutPayload,
    csrfToken: string,
) => {
    const body = new URLSearchParams();
    body.set('_token', csrfToken);

    Object.entries(payload).forEach(([key, value]) => {
        if (value !== null && value !== undefined) {
            body.set(key, String(value));
        }
    });

    const response = await fetch(endpoint, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body,
    });

    const data = await response.json().catch(() => null) as {
        checkout_url?: string;
        message?: string;
        errors?: Record<string, string[]>;
    } | null;

    if (!response.ok) {
        throw new Error(responseMessage(data, 'PhonePe payment could not be started.'));
    }

    const checkoutUrl = data?.checkout_url;
    if (!checkoutUrl || !/^https:\/\//i.test(checkoutUrl)) {
        throw new Error('PhonePe did not return a valid checkout page.');
    }

    try {
        await loadCheckoutScript();
        if (!window.PhonePeCheckout?.transact) {
            throw new Error('PhonePe checkout is unavailable.');
        }

        window.PhonePeCheckout.transact({ tokenUrl: checkoutUrl });
    } catch {
        window.location.assign(checkoutUrl);
    }
};

