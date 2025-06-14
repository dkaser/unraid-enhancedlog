const minDate = {};
const maxDate = {};

DataTable.ext.search.push(function (settings, data, dataIndex) {
    if (minDate[settings.sTableId] === undefined || maxDate[settings.sTableId] === undefined) {
        return true;
    }

    const minVal = minDate[settings.sTableId].val();
    const maxVal = maxDate[settings.sTableId].val();
    const dateVal = new Date(data[0]);

    if (minVal === null && maxVal === null) {
        return true;
    }

    let min = (minVal === null) ? luxon.DateTime.fromMillis(0) : luxon.DateTime.fromJSDate(minVal);
    let max = (maxVal === null) ? luxon.DateTime.now().plus({ hours: 1}) : luxon.DateTime.fromJSDate(maxVal);
    let date = luxon.DateTime.fromJSDate(dateVal);

    min = min.minus({ minutes: min.offset });
    max = max.minus({ minutes: max.offset });

    min = min.toJSDate();
    max = max.toJSDate();
    date = date.toJSDate();
    if (
        (min <= date && date <= max)
    ) {
        return true;
    }
    return false;
});

DataTable.feature.register('dateRange', function (settings, opts) {
    let toolbar = document.createElement('div');
    toolbar.appendChild(document.createTextNode('From: '));

    const minInput = document.createElement('input');
    minInput.id = 'min-' + settings.sTableId;
    minInput.name = 'min-' + settings.sTableId;
    minInput.type = 'text';
    toolbar.appendChild(minInput);

    toolbar.appendChild(document.createTextNode(' To: '));
    const maxInput = document.createElement('input');
    maxInput.id = 'max-' + settings.sTableId;
    maxInput.name = 'max-' + settings.sTableId;
    maxInput.type = 'text';
    toolbar.appendChild(maxInput);

    const dateSettings = {
        format: 'D HH:mm',
        buttons: {
            clear: true
        }
    }

    minDate[settings.sTableId] = new DateTime(minInput, dateSettings);
    maxDate[settings.sTableId] = new DateTime(maxInput, dateSettings);

    minInput.addEventListener('change', () => settings.api.draw());
    maxInput.addEventListener('change', () => settings.api.draw());

    return toolbar;
});

DataTable.feature.register('logSelect', function (settings, opts) {
    let toolbar = document.createElement('div');
    toolbar.appendChild(document.createTextNode('Log: '));

    const logSelect = document.createElement('select');
    logSelect.id = 'log-select-' + settings.sTableId;
    logSelect.name = 'log-select-' + settings.sTableId;
    
    for (const [key, value] of Object.entries(logFiles)) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = key;
        logSelect.appendChild(option);
    }

    toolbar.appendChild(logSelect);
    
    logSelect.addEventListener('change', function (e) {
        const newUrl = opts.baseURL + '?log=' + encodeURIComponent(e.target.value);
        settings.api.ajax.url(newUrl).load();
    });

    return toolbar;
});

function getDatatableConfig(url, refreshText, tableName) {
    return {
        tableName: tableName,
        ajax: {
            url: url,
            dataSrc: ''
        },
        order: [[0, 'desc']],
        columns: [
            { name: "date", data: null, render: {
                _: 'date',
                filter: 'sequence',
                display: 'date'
            }},
            { name: "source", data: 'source' },
            { name: "service", data: null, render: {
                _: 'service',
                filter: 'serviceFilter',
                display: 'service'
            }},
            { name: "message", data: 'message' },
            { name: "matchType", data: 'matchType' },
        ],
        columnControl: {
            target: 0,
            content: [{
                extend: 'dropdown',
                content: ['searchClear', 'search'],
                icon: 'search'
            }]
        },
        columnDefs: [
            {
                targets: 0,
                className: 'dt-head-left',
                columnControl: {
                    target: 0,
                    content: []
                }
            },
            {
                targets: [1,2,4],
                columnControl: {
                    target: 0,
                    content: [{
                        extend: 'dropdown',
                        content: ['searchClear', 'searchList'],
                        icon: 'search'
                    }]
                }
            }
        ],
        paging: true,
        pageLength: 50,
        ordering: true,
        layout: {
            topStart: {
                buttons: [
                    {
                        text: refreshText,
                        action: function ( e, dt, node, config ) {
                            dt.ajax.reload();
                        }
                    },
                    {
                        text: "Clear Filters",
                        action: function ( e, dt, node, config ) {
                            minDate[dt.settings()[0].sTableId].val(null);
                            maxDate[dt.settings()[0].sTableId].val(null);
                            dt.search('');
                            dt.columns().ccSearchClear();
                            dt.draw();
                        }
                    }
                ],
                logSelect: {
                    baseURL: url
                },
                pageLength: {
                    menu: [25, 50, 100, 200, -1]
                }
            },
            topEnd: {
                dateRange: {}
            }
        },
         "createdRow": function( row, data, dataIndex){
                if( data["color"] != ""){
                    row.style.backgroundColor = data.color;
                }
            }
    };
}

function getSummaryConfig(url, refreshText, tableName) {
    return {
        tableName: tableName,
        ajax: {
            url: url,
            dataSrc: ''
        },
        order: [[0, 'desc']],
        columns: [
            { name: "count", data: "count" },
            { name: "source", data: 'source' },
            { name: "service", data: "service" },
            { name: "message", data: 'message' },
            { name: "matchType", data: 'matchType' },
        ],
        columnControl: {
            target: 0,
            content: [{
                extend: 'dropdown',
                content: ['searchClear', 'search'],
                icon: 'search'
            }]
        },
        columnDefs: [
            {
                targets: 0,
                className: 'dt-head-left'
            },
            {
                targets: [1,2,4],
                columnControl: {
                    target: 0,
                    content: [{
                        extend: 'dropdown',
                        content: ['searchClear', 'searchList'],
                        icon: 'search'
                    }]
                }
            }
        ],
        paging: true,
        pageLength: 50,
        ordering: true,
        layout: {
            topStart: {
                buttons: [
                    {
                        text: refreshText,
                        action: function ( e, dt, node, config ) {
                            dt.ajax.reload();
                        }
                    },
                    {
                        text: "Clear Filters",
                        action: function ( e, dt, node, config ) {
                            minDate[dt.settings()[0].sTableId].val(null);
                            maxDate[dt.settings()[0].sTableId].val(null);
                            dt.search('');
                            dt.columns().ccSearchClear();
                            dt.draw();
                        }
                    }
                ],
                logSelect: {
                    baseURL: url
                },
                pageLength: {
                    menu: [25, 50, 100, 200, -1]
                }
            },
            topEnd: {
                dateRange: {}
            }
        },
         "createdRow": function( row, data, dataIndex){
                if( data["color"] != ""){
                    row.style.backgroundColor = data.color;
                }
            }
    };
}
