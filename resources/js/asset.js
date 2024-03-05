import InitZadarmaCall from "./components/InitZadarmaCall";
import loadScript from "./scriptLoader";


Nova.booting((app) => {
  const canCall = Nova.config('zadarma_can_call');
  console.log('canCall??', canCall);
  const loadZadarma = () => {
    loadScript(
      "https://my.zadarma.com/webphoneWebRTCWidget/v8/js/loader-phone-lib.js?v=68",
    ).then(() => {

      loadScript("/nova-vendor/webard/nova-zadarma/zadarma-loader-phone-fn.js?v=68").then(() => {
        setTimeout(() => {
          zadarmaWidgetFn(
            Nova.config("zadarma_key"),
            Nova.config("zadarma_login"),
            "rounded" /*square|rounded*/,
            "en" /*ru, en, es, fr, de, pl, ua*/,
            true,
            {
              right: "25px",
              bottom: "90px",
              getStatusMessage: function (status) {
                alert("status:" + status);
              },
            },
          );
        }, 1000);

        //window.zdrmWebPhone.zadarmaCallbackCancel(function() {alert('sdfds');})
      });
    });
  };

  app.component("InitZadarmaCall", InitZadarmaCall);

  loadZadarma();
});
