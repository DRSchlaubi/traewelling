/**
 * Here, we include all of our external dependencies
 */

require("./bootstrap");
require("awesomplete/awesomplete");
require("leaflet/dist/leaflet.js");

/**
 * Once the page is loaded, we can load our frontend components.
 */
window.addEventListener("load", () => {
    require("./components/alert");
    require("./components/notifications-board");
    require("./components/progressbar");
    require("./components/settings");
    require("./components/station-autocomplete");
    require("./components/stationboard");
    require("./components/statusMap");
    require("./components/timepicker");
    require("./components/business-check-in");
    require("./../../node_modules/bootstrap/js/dist/modal");
    require("./appControls");
    require("bootstrap-cookie-alert/cookiealert");
});
