import type { AddOn, CakeSize, OrderSummary } from '@/types/cake-shop';

export const cakeSizes: CakeSize[] = [
    { id: '20', name: '20 cm', price: 150000 },
    { id: '22', name: '22 cm', price: 180000 },
    { id: '25', name: '25 cm', price: 220000 },
];

export const addOns: AddOn[] = [
    { id: 'knife', name: 'Pisau', price: 2000, unit: 'pcs' },
    { id: 'plate', name: 'Piring', price: 500, unit: 'pcs' },
    { id: 'candle', name: 'Lilin', price: 1000, unit: 'pcs' },
    { id: 'topper', name: 'Topper', price: 15000, unit: 'pcs' },
];

export const recentOrders: OrderSummary[] = [
    {
        id: 'ORD-01JY9QTX',
        customerName: 'Nadia Putri',
        pickupAt: 'Hari ini, 15.00',
        total: 202000,
        status: 'pending',
    },
    {
        id: 'ORD-01JY9QSY',
        customerName: 'Rizky Maulana',
        pickupAt: 'Besok, 10.00',
        total: 220000,
        status: 'completed',
    },
    {
        id: 'ORD-01JY9QRZ',
        customerName: 'Aulia Rahman',
        pickupAt: '25 Jun, 13.30',
        total: 167000,
        status: 'pending',
    },
];
