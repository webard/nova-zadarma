import PhoneCallHeaderIcon from "./components/PhoneCallHeaderIcon";
import PhoneCallWidget from "./components/PhoneCallWidget";
import { createApp, defineComponent } from "vue";
import InitZadarmaCall from "./components/InitZadarmaCall";
import loadScript from "./scriptLoader";


Nova.booting((app) => {
   const loadZadarma = () => {
    loadScript(
      "https://my.zadarma.com/webphoneWebRTCWidget/v8/js/loader-phone-lib.js?v=68",
    ).then(() => {

      loadScript("/nova-vendor/webard/nova-zadarma/zadarma-loader-phone-fn.js?v=68").then(() => {
        setTimeout(() => {
          zadarmaWidgetFn(
            Nova.config("zadarma_key"),
            Nova.config("zadarma_login"),
            Nova.config("zadarma_widget").shape,
            Nova.config("zadarma_widget").language,
            true,
            Nova.config("zadarma_widget").position,
          );
        }, 1000);
      });
    });
  };

  app.component("InitZadarmaCall", InitZadarmaCall);

  const canCall = Nova.config('zadarma_can_call');

  if (canCall == true || canCall == 'true') {
    if ( Nova.config("zadarma_key") == null || Nova.config("zadarma_login") == null) {
      Nova.error("Zadarma key or login is not set, probably SIP is not configured or API keys are not set. Please check your configuration.");
      console.error("Zadarma key or login is not set, probably SIP is not configured or API keys are not set. Please check your configuration.");
    } else {
      loadZadarma();
    }
  }
});


Nova.booting((app) => {
  const mountPhoneCallHeaderIcon = () => {
    let appHeader = document.getElementsByTagName("header");

    if (appHeader.length > 0) {
      let component = defineComponent({
        extends: PhoneCallHeaderIcon,
        data() {
          return { test: true };
        },
      });

      let div = document.createElement("div");
      // lang.className = 'mr-3';
      let newApp = createApp(component);

      newApp.component(
        "Icon",
        app._context.components.HeroiconsOutlinePhoneOutgoing,
      );

      newApp.mount(div);

      appHeader[0].lastChild.lastChild.insertBefore(
        div,
        appHeader[0].lastChild.lastChild.firstChild,
      );
    }
  };

  const mountPhoneCallWidget = () => {
    let content = document.querySelector('[dusk="content"]');

    if (content !== null) {
      let component = defineComponent({
        extends: PhoneCallWidget,
        data() {
          return { test: true };
        },
      });

      let div = document.createElement("div");

      let newApp = createApp(component);

      newApp.mount(div);

      content.lastChild.lastChild.insertBefore(
        div,
        content.lastChild.lastChild.firstChild,
      );
    }
  };

  window.addEventListener("DOMContentLoaded", () => {
    mountPhoneCallHeaderIcon();
    mountPhoneCallWidget();
  });
});
