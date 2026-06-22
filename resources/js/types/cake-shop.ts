export type OrderStatus = 'pending' | 'completed' | 'cancelled';

export type OrderSummary = {
    id: string;
    customerName: string;
    pickupAt: string;
    total: number;
    status: OrderStatus;
};

export type CakeSize = { id: string; name: string; price: number };

export type AddOn = { id: string; name: string; price: number; unit: string };
