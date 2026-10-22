import { defineBundle, HeadService, SectionManager } from '@saolabs/client';

export default defineBundle({
    name: 'web', providers: [], services: {}, helpers: {},
    boot(app) {
        const origin = new URL(document.querySelector<HTMLLinkElement>('link[rel="canonical"]')?.href || location.href).origin;
        // SPA navigation keeps the server <head>; refresh what crawlers and share previews read.
        const initializePage = () => {
            const controller = app.View.getCurrentView()?.__ctrl__;
            if (controller) {
                for (const section of controller.sections.values()) {
                    if (section.name.startsWith('meta:')) SectionManager.add(section);
                }
                SectionManager.mountViewSections(controller.viewId);
            }
            const canonical = origin + location.pathname;
            HeadService.setLink('canonical', canonical);
            HeadService.setMetaProperty('og:url', canonical);
        };
        app.Router.afterEach(initializePage);
        initializePage();
    },
});
