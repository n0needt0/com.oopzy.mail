


//Set the require.js configuration for your application.

require.config({
  
  paths: {
    // Libraries
    jquery: Conf.home + "/assets/vendor/jquery/jquery",
    jqueryui: Conf.home + "/assets/vendor/jquery.ui/jquery.ui",
    underscore: Conf.home + "/assets/vendor/underscore",
    backbone: Conf.home + "/assets/vendor/backbone",
    json2: Conf.home + "/assets/vendor/json2",
    // Shim Plugin
    use: Conf.home + "/assets/vendor/require/plugins/use",
    async: Conf.home + "/assets/vendor/require/plugins/async"
  },

  use: {
    backbone: {
      deps: ["underscore", "jquery", "json2","jqueryui"],
      attach: "Backbone"
    },

    underscore: {
      attach: "_"
    }
  },
  
//Initialize the application with the main application file
  deps: [
         Conf.home + "/assets/jsapp/mail.js?" + new Date().getTime(),
         Conf.home + "/assets/jsapp/templates_mail.js"
         ]
   
});

