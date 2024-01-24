import { AxiosInstance } from "axios";
import routeFn from "ziggy-js";
import type { PageProps as InertiaPageProps } from '@inertiajs/core';
import type { CustomPageProps } from './inertia';

declare global {
    interface Window {
        axios: AxiosInstance;
    }
    var route: typeof routeFn;

}

declare module "vue" {
    interface ComponentCustomProperties {
        route: typeof routeFn;
    }
}

declare module '@inertiajs/core' {
    interface PageProps extends CustomPageProps {}
}