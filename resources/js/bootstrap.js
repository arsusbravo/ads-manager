/**
 * CSRF-aware fetch wrapper used across all Vue components.
 * Reads the XSRF-TOKEN cookie Laravel sets and injects it as a header.
 */
function getCsrfToken() {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
}

window.api = async function (url, options = {}) {
    const { body, method = body ? 'POST' : 'GET', ...rest } = options;

    const response = await fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-XSRF-TOKEN': getCsrfToken(),
            ...(rest.headers || {}),
        },
        ...(body !== undefined ? { body: JSON.stringify(body) } : {}),
        ...rest,
    });

    if (!response.ok) {
        const error = await response.json().catch(() => ({ message: response.statusText }));
        throw Object.assign(new Error(error.message || 'Request failed'), { status: response.status, data: error });
    }

    return response.status === 204 ? null : response.json();
};
