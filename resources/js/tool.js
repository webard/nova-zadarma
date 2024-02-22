import CurrentPhoneCall from "./components/CurrentPhoneCall";
import CallWidget from "./components/CallWidget";
import { createApp, defineComponent } from "vue";

Nova.booting((app) => {
  const mountPhoneCallHeaderIcon = () => {
    let appHeader = document.getElementsByTagName("header");

    if (appHeader.length > 0) {
      let component = defineComponent({
        extends: CurrentPhoneCall,
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
        extends: CallWidget,
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
