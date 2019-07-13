Perch.UserConfig.redactor = (function() {
  var get = function(profile, config, field) {
    if (config.plugins.indexOf("alignment") === -1)
      config.plugins.push("alignment");

    config.plugins = ["alignment"];
    config.buttons = [
      "html",
      "format",
      "bold",
      "italic",
      "lists",
      "link",
      "fullscreen",
      "alignment"
    ];
    config.formatting = ["p", "h3", "h4"];

    return config;
  };

  var load = function(cb) {
    jQuery.getScript(
      Perch.path + "/addons/plugins/editors/redactor-plugins/alignment.js",
      cb
    );
  };

  return {
    get: get,
    load: load
  };
})();
