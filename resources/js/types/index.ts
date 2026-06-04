export interface User {
    id: number
    name: string
    email: string
    email_verified_at?: string | null
}

export interface Tag {
    id: number
    name: string
}

export interface Recipe {
    id: number
    title: string
    body: string
    prep_minutes: number
    is_draft: boolean
    author: User
    tags: Tag[]
}

export interface RecipeForm {
    title: string
    body: string
    prep_minutes: number
}

export interface FlashMessages {
    success?: string | null
    error?: string | null
}

export interface PageProps {
    appName: string
    locale: string
    currentYear: number
    auth: {
        user: User | null
        recentRecipes?: Recipe[]
    }
    flash?: FlashMessages
    activeDraft?: Recipe | null
}
