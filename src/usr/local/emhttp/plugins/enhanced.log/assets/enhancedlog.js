const minDate = {};
const maxDate = {};

DataTable.ext.search.push(function (settings, data, dataIndex) {
  if (
    minDate[settings.sTableId] === undefined ||
    maxDate[settings.sTableId] === undefined
  ) {
    return true;
  }

  const minVal = minDate[settings.sTableId].selectedDates;
  const maxVal = maxDate[settings.sTableId].selectedDates;
  const dateVal = luxon.DateTime.fromFormat(
    data[0],
    "MMM d HH:mm:ss"
  ).toJSDate();

  const minValEmpty = !Array.isArray(minVal) || !minVal.length;
  const maxValEmpty = !Array.isArray(maxVal) || !maxVal.length;

  if (minValEmpty && maxValEmpty) {
    return true;
  }

  let min = minValEmpty ? luxon.DateTime.fromMillis(0).toJSDate() : minVal[0];
  let max = maxValEmpty
    ? luxon.DateTime.now().plus({ hours: 1 }).toJSDate()
    : maxVal[0];

  if (min <= dateVal && dateVal <= max) {
    return true;
  }
  return false;
});

DataTable.feature.register("dateRange", function (settings, opts) {
  let toolbar = document.createElement("div");
  toolbar.appendChild(document.createTextNode(`${translator.tr("from")}: `));

  const minInput = document.createElement("input");
  minInput.id = "min-" + settings.sTableId;
  minInput.name = "min-" + settings.sTableId;
  minInput.type = "text";
  toolbar.appendChild(minInput);

  toolbar.appendChild(document.createTextNode(`${translator.tr("to")}: `));
  const maxInput = document.createElement("input");
  maxInput.id = "max-" + settings.sTableId;
  maxInput.name = "max-" + settings.sTableId;
  maxInput.type = "text";
  toolbar.appendChild(maxInput);

  const dateSettings = {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
  };

  minDate[settings.sTableId] = new flatpickr(minInput, dateSettings);
  maxDate[settings.sTableId] = new flatpickr(maxInput, dateSettings);

  minInput.addEventListener("change", () => settings.api.draw());
  maxInput.addEventListener("change", () => settings.api.draw());

  return toolbar;
});

DataTable.feature.register("logSelect", function (settings, opts) {
  let toolbar = document.createElement("div");
  toolbar.appendChild(document.createTextNode(`${translator.tr("log")}: `));
  toolbar.className = "log-select";

  const logSelect = document.createElement("select");
  logSelect.id = "log-select-" + settings.sTableId;
  logSelect.name = "log-select-" + settings.sTableId;
  logSelect.className = "log-select";

  for (const [key, value] of Object.entries(logFiles)) {
    const option = document.createElement("option");
    option.value = value;
    option.textContent = key;
    logSelect.appendChild(option);
  }

  toolbar.appendChild(logSelect);

  logSelect.addEventListener("change", function (e) {
    const newUrl = opts.baseURL + "?log=" + encodeURIComponent(e.target.value);
    settings.api.ajax.url(newUrl).load();
  });

  return toolbar;
});

DataTable.feature.register("pluginSelect", function (settings, opts) {
  let toolbar = document.createElement("div");
  toolbar.appendChild(document.createTextNode(`${translator.tr("log")}: `));
  toolbar.className = "log-select";

  const logSelect = document.createElement("select");
  logSelect.id = "log-select-" + settings.sTableId;
  logSelect.name = "log-select-" + settings.sTableId;
  logSelect.className = "log-select";

  const noneOption = document.createElement("option");
  noneOption.value = "";
  noneOption.textContent = "";
  logSelect.appendChild(noneOption);

  for (const [key, value] of Object.entries(pluginFiles)) {
    const option = document.createElement("option");
    option.value = value;
    option.textContent = key;
    logSelect.appendChild(option);
  }

  toolbar.appendChild(logSelect);

  logSelect.addEventListener("change", function (e) {
    const newUrl = opts.baseURL + "?log=" + encodeURIComponent(e.target.value);
    settings.api.ajax.url(newUrl).load();
  });

  return toolbar;
});

const columnSaving = {
  columns: {
    search: true,
    visible: false,
  },
  length: true,
  order: true,
  paging: true,
  scroller: false,
  search: false,
  searchBuilder: false,
  searchPanes: false,
  select: true,
};

function getDatatableConfig(url) {
  return {
    ajax: {
      url: url,
      dataSrc: "",
    },
    order: [[0, "desc"]],
    columns: [
      {
        name: "date",
        data: null,
        type: "num",
        render: {
          _: "date",
          sort: "sequence",
        },
      },
      { name: "source", data: "source" },
      {
        name: "service",
        data: null,
        render: {
          _: "service",
          filter: "serviceFilter",
        },
      },
      { name: "message", data: "message" },
      { name: "matchType", data: "matchType" },
    ],
    columnControl: {
      target: 0,
      content: [
        {
          extend: "dropdown",
          content: ["searchClear", "search"],
          icon: "search",
        },
      ],
    },
    columnDefs: [
      {
        targets: 0,
        className: "dt-head-left",
        columnControl: {
          target: 0,
          content: [],
        },
      },
      {
        targets: [1, 2, 4],
        columnControl: {
          target: 0,
          content: [
            {
              extend: "dropdown",
              content: ["searchClear", "searchList"],
              icon: "search",
            },
          ],
        },
      },
      {
        targets: 3,
        className: "overflow-anywhere",
      },
    ],
    paging: true,
    pageLength: 50,
    ordering: true,
    stateSave: true,
    layout: {
      top2Start: {
        buttons: [
          {
            text: translator.tr("refresh"),
            action: function (e, dt, node, config) {
              dt.ajax.reload();
            },
          },
          {
            extend: "csv",
            text: translator.tr("download"),
          },
          "copy",
        ],
        logSelect: {
          baseURL: url,
        },
        pageLength: {
          menu: [25, 50, 100, 200, -1],
        },
      },
      top2End: {
        dateRange: {},
      },
      topStart: {
        buttons: [
          {
            text: translator.tr("clear_filters"),
            action: function (e, dt, node, config) {
              minDate[dt.settings()[0].sTableId].clear();
              maxDate[dt.settings()[0].sTableId].clear();
              dt.search("");
              dt.columns().ccSearchClear();
              dt.draw();
            },
          },
          { extend: "createState", text: translator.tr("save") },
          {
            extend: "savedStates",
            config: {
              creationModal: true,
              toggle: columnSaving,
              saveState: columnSaving,
            },
          },
        ],
      },
      topEnd: {},
    },
    createdRow: function (row, data, dataIndex) {
      if (data["color"] != "") {
        row.style.backgroundColor = data.color;
      }
      if (data["textColor"] != "") {
        row.style.color = data.textColor;
      }
    },
  };
}

function getSummaryConfig(url) {
  return {
    ajax: {
      url: url,
      dataSrc: "",
    },
    order: [[0, "desc"]],
    columns: [
      { name: "count", data: "count" },
      { name: "source", data: "source" },
      { name: "service", data: "service" },
      { name: "message", data: "message" },
      { name: "matchType", data: "matchType" },
    ],
    columnControl: {
      target: 0,
      content: [
        {
          extend: "dropdown",
          content: ["searchClear", "search"],
          icon: "search",
        },
      ],
    },
    columnDefs: [
      {
        targets: 0,
        className: "dt-head-left",
      },
      {
        targets: [1, 2, 4],
        columnControl: {
          target: 0,
          content: [
            {
              extend: "dropdown",
              content: ["searchClear", "searchList"],
              icon: "search",
            },
          ],
        },
      },
      {
        targets: 3,
        className: "overflow-anywhere",
      },
    ],
    paging: true,
    pageLength: 50,
    ordering: true,
    stateSave: true,
    layout: {
      top2Start: {
        buttons: [
          {
            text: translator.tr("refresh"),
            action: function (e, dt, node, config) {
              dt.ajax.reload();
            },
          },
          {
            extend: "csv",
            text: translator.tr("download"),
          },
          "copy",
        ],
        logSelect: {
          baseURL: url,
        },
        pageLength: {
          menu: [25, 50, 100, 200, -1],
        },
      },
      top2End: {
        dateRange: {},
      },
      topStart: {
        buttons: [
          {
            text: translator.tr("clear_filters"),
            action: function (e, dt, node, config) {
              minDate[dt.settings()[0].sTableId].clear();
              maxDate[dt.settings()[0].sTableId].clear();
              dt.search("");
              dt.columns().ccSearchClear();
              dt.draw();
            },
          },
          { extend: "createState", text: translator.tr("save") },
          {
            extend: "savedStates",
            config: {
              creationModal: true,
              toggle: columnSaving,
              saveState: columnSaving,
            },
          },
        ],
      },
      topEnd: {},
    },
    createdRow: function (row, data, dataIndex) {
      if (data["color"] != "") {
        row.style.backgroundColor = data.color;
      }
      if (data["textColor"] != "") {
        row.style.color = data.textColor;
      }
    },
  };
}

function getPluginConfig(url) {
  return {
    ajax: {
      url: url,
      dataSrc: "",
    },
    order: [[0, "desc"]],
    columns: [
      { name: "sequence", data: "sequence" },
      { name: "line", data: "line" },
    ],
    columnDefs: [
      {
        targets: 0,
        width: 1,
        className: "dt-left",
        columnControl: {
          target: 0,
          content: [],
        },
      },
      {
        targets: 1,
        className: "overflow-anywhere",
        columnControl: {
          target: 0,
          content: [
            {
              extend: "dropdown",
              content: ["searchClear", "search"],
              icon: "search",
            },
          ],
        },
      },
    ],
    paging: true,
    pageLength: 50,
    ordering: true,
    stateSave: true,
    layout: {
      topStart: {
        buttons: [
          {
            text: translator.tr("refresh"),
            action: function (e, dt, node, config) {
              dt.ajax.reload();
            },
          },
          {
            extend: "csv",
            text: translator.tr("download"),
          },
          "copy",
          {
            text: translator.tr("clear_filters"),
            action: function (e, dt, node, config) {
              dt.search("");
              dt.columns().ccSearchClear();
              dt.draw();
            },
          },
        ],
        pluginSelect: {
          baseURL: url,
        },
        pageLength: {
          menu: [25, 50, 100, 200, -1],
        },
      },
      topEnd: {},
    },
  };
}
