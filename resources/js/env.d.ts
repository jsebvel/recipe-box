/// <reference types="vite/client" />

import type { User, Recipe } from './types'

declare module '*.vue' {
    import type { DefineComponent } from 'vue'
    const component: DefineComponent<Record<string, never>, Record<string, never>, unknown>
    export default component
}

interface ImportMetaEnv {
    readonly VITE_APP_NAME: string
}

interface ImportMeta {
    readonly env: ImportMetaEnv
}

/**
 * Augment Inertia's PageProps with our Laravel-specific shared data.
 * Now `usePage().props.auth.user` will be typed correctly.
 */
declare module '@inertiajs/core' {
    interface PageProps {
        appName?: string
        locale?: string
        currentYear?: number
        auth?: {
            user: User | null
            recentRecipes?: Recipe[]
        }
        flash?: {
            success?: string | null
            error?: string | null
        }
        activeDraft?: Recipe | null
    }
}

/**
 * Make Ziggy's route() available globally to TS in Vue templates.
 */
declare module 'vue' {
    interface ComponentCustomProperties {
        route: (
            name?: string | undefined,
            params?: Record<string, unknown> | unknown[] | string | number,
            absolute?: boolean,
        ) => string & {
            current: (name?: string) => boolean
        }
    }
}

declare global {
    interface Window {
        axios: typeof import('axios').default
    }

    function route(
        name?: string | undefined,
        params?: Record<string, unknown> | unknown[] | string | number,
        absolute?: boolean,
    ): string & {
        current: (name?: string) => boolean
    }
}

export {}

