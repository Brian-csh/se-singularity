// Pass single element
const element = document.getElementById('multiple-select-search');
const hidden = document.getElementById(hidden_name);

const choices = new Choices(element, {
    silent: false,
    items: [],
    choices: asset_classes,
    renderChoiceLimit: 100,
    maxItemCount: -1,
    addItems: true,
    addItemFilter: null,
    removeItems: true,
    removeItemButton: true,
    editItems: false,
    allowHTML: true,
    duplicateItemsAllowed: false,
    delimiter: ',',
    paste: false,
    searchEnabled: true,
    searchChoices: true,
    searchFloor: 1,
    searchResultLimit: 4,
    searchFields: ['label', 'value'],
    position: 'bottom',
    resetScrollPosition: false,
    shouldSort: false,
    shouldSortItems: false,
    placeholder: true,
    placeholderValue: "Select class...",
    searchPlaceholderValue: null,
    prependValue: null,
    appendValue: null,
    renderSelectedChoices: 'auto',
    loadingText: 'Loading...',
    noResultsText: 'No results found',
    noChoicesText: 'No choices to choose from',
    itemSelectText: 'Press to select',
    uniqueItemText: 'Only unique values can be added',
    customAddItemText: 'Only values matching specific conditions can be added',
    addItemText: (value) => {
        return `Press Enter to add <b>"${value}"</b>`;
    },
    maxItemText: (maxItemCount) => {
        return `Only ${maxItemCount} values can be added`;
    },
    valueComparer: (value1, value2) => {
        return value1 === value2;
    },
    classNames: {
        containerOuter: 'choices',
        containerInner: 'choices__inner',
        input: 'choices__input',
        inputCloned: 'choices__input--cloned',
        list: 'choices__list',
        listItems: 'choices__list--multiple',
        listSingle: 'choices__list--single',
        listDropdown: 'choices__list--dropdown',
        item: 'choices__item',
        itemSelectable: 'choices__item--selectable',
        itemDisabled: 'choices__item--disabled',
        itemChoice: 'choices__item--choice',
        placeholder: 'choices__placeholder',
        group: 'choices__group',
        groupHeading: 'choices__heading',
        button: 'choices__button',
        activeState: 'is-active',
        focusState: 'is-focused',
        openState: 'is-open',
        disabledState: 'is-disabled',
        highlightedState: 'is-highlighted',
        selectedState: 'is-selected',
        flippedState: 'is-flipped',
        loadingState: 'is-loading',
        noResults: 'has-no-results',
        noChoices: 'has-no-choices'
    },
    // Choices uses the great Fuse library for searching. You
    // can find more options here: https://fusejs.io/api/options.html
    fuseOptions: {
        includeScore: true
    },
    labelId: '',
    callbackOnInit: null,
    callbackOnCreateTemplates: null,
});
element.addEventListener(
    'addItem',
    function(event) {
        hidden.value = choices.getValue(true);
    },
    false,
  );

  element.addEventListener(
    'removeItem',
    function(event) {
        hidden.value = choices.getValue(true);
    },
    false,
  );
