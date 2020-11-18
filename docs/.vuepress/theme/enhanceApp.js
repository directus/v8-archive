import { Tabs, Tab } from "vue-tabs-component";

export default ({ Vue }) => {
    Vue.component("tabs", Tabs);
    Vue.component("tab", Tab);
};
