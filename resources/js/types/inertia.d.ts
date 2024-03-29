export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
}
export type CustomPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
    [key: string]: unknown;
    
};