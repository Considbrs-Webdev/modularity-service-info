import { createViteConfig } from "vite-config-factory";

const entries = {
    'css/modularity-service-info': './source/sass/modularity-service-info.scss',
    'js/modularity-service-info': './source/js/modularity-service-info.js',
};

export default createViteConfig(entries, {
    outDir: "assets/dist",
    manifestFile: "manifest.json",
});
