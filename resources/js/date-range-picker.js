import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';

/**
 * Initialize date range pickers on elements with data-date-range attribute.
 * Expects a form ancestor - on change, updates hidden date_from/date_to and submits.
 * Display: dd/mm/yyyy - dd/mm/yyyy. Calendar stays open until second date selected.
 */
export function initDateRangePickers() {
    document.querySelectorAll('[data-date-range]').forEach((el) => {
        if (el._flatpickr) return;

        const form = el.closest('form');
        const dateFromInput = form?.querySelector('input[name="date_from"]');
        const dateToInput = form?.querySelector('input[name="date_to"]');

        const fromVal = dateFromInput?.value?.trim() || '';
        const toVal = dateToInput?.value?.trim() || '';
        const parseDate = (str) => {
            if (!str || !/^\d{4}-\d{2}-\d{2}$/.test(str)) return null;
            const d = new Date(str + 'T12:00:00');
            return isNaN(d.getTime()) ? null : d;
        };
        const defaultDate = fromVal && toVal
            ? [parseDate(fromVal), parseDate(toVal)].filter(Boolean)
            : fromVal
                ? [parseDate(fromVal)].filter(Boolean)
                : [];

        const formatDisplay = (dates) => {
            if (dates.length === 0) return '';
            const fmt = (d) => flatpickr.formatDate(d, 'd/m/Y');
            if (dates.length === 1) return fmt(dates[0]);
            return `${fmt(dates[0])} - ${fmt(dates[1])}`;
        };

        const fp = flatpickr(el, {
            defaultDate,
            mode: 'range',
            locale: { firstDayOfWeek: 1 },
            dateFormat: 'Y-m-d',
            allowInput: false,
            disableMobile: true,
            appendTo: document.body,
            onDayCreate(_dObj, _dStr, instance, dayElem) {
                dayElem.setAttribute('role', 'button');
                dayElem.setAttribute('tabindex', '0');
                const d = dayElem.dateObj;
                if (d instanceof Date) {
                    dayElem.setAttribute('aria-label', `Select ${flatpickr.formatDate(d, 'd/m/Y')}`);
                }
            },
            onReady(selectedDates, dateStr, instance) {
                instance.calendarContainer.classList.add('filter-datepicker');
                if (document.documentElement.classList.contains('dark')) {
                    instance.calendarContainer.classList.add('dark');
                }
                const observer = new MutationObserver(() => {
                    instance.calendarContainer.classList.toggle(
                        'dark',
                        document.documentElement.classList.contains('dark')
                    );
                });
                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class'],
                });
                // Initial display format dd/mm/yyyy when page loads with dates
                if (selectedDates.length >= 1) {
                    (instance.altInput ?? el).value = formatDisplay(selectedDates);
                }
            },
            onChange(selectedDates, dateStr, instance) {
                const input = instance.altInput ?? el;
                if (dateFromInput && dateToInput) {
                    if (selectedDates.length >= 1) {
                        dateFromInput.value = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
                    }
                    if (selectedDates.length >= 2) {
                        dateToInput.value = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
                        input.value = formatDisplay(selectedDates);
                        form?.submit();
                    } else if (selectedDates.length === 1) {
                        dateToInput.value = '';
                        input.value = formatDisplay(selectedDates);
                        // Do NOT submit – keep calendar open for second date
                    }
                }
            },
            onClose(selectedDates, dateStr, instance) {
                // Same-date range: one date selected and user closes → treat as from=to
                if (
                    selectedDates?.length === 1 &&
                    dateFromInput &&
                    dateToInput &&
                    dateToInput.value === ''
                ) {
                    const d = selectedDates[0];
                    dateFromInput.value = flatpickr.formatDate(d, 'Y-m-d');
                    dateToInput.value = flatpickr.formatDate(d, 'Y-m-d');
                    (instance.altInput ?? el).value = formatDisplay([d, d]);
                    form?.submit();
                }
            },
        });

        el._flatpickr = fp;
    });
}
