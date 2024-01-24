/**
 * This would normally be generated automatically, but for the sake of simplicity...
 */

export interface NannyBooking {
    id: number;
    title: string;
    price: number;
    start_at: string;
    end_at: string;
    user : User;
}

export interface User {
    id: number;
    email: string;
    name: string;
}