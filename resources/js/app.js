const initializeCurrencySwap = () => {
    const swapButton = document.getElementById('swap-currencies');
    const sourceSelect = document.getElementById('from');
    const targetSelect = document.getElementById('to');
    const swapStatus = document.getElementById('swap-status');

    if (! swapButton || ! sourceSelect || ! targetSelect) {
        return;
    }

    swapButton.addEventListener('click', () => {
        const previousSource = sourceSelect.value;

        sourceSelect.value = targetSelect.value;
        targetSelect.value = previousSource;

        sourceSelect.dispatchEvent(new Event('change', { bubbles: true }));
        targetSelect.dispatchEvent(new Event('change', { bubbles: true }));

        if (swapStatus) {
            swapStatus.textContent = `Source currency is now ${sourceSelect.value}; target currency is now ${targetSelect.value}.`;
        }
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCurrencySwap, { once: true });
} else {
    initializeCurrencySwap();
}
