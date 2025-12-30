import { createViteConfig } from "vite-config-factory";

const entries = {
    'css/modularity-service-info-general': './source/sass/modularity-service-info-general.scss',
    'css/modularity-service-info-module': './source/sass/modularity-service-info-module.scss',
    'js/modularity-service-info': './source/js/modularity-service-info.js',
};

export default createViteConfig(entries, {
    outDir: "assets/dist",
    manifestFile: "manifest.json",
});
