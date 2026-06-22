import { Head } from '@inertiajs/react';
import { PageHeader } from '@/components/page-header';
import { StatusBadge } from '@/components/status-badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { formatRupiah } from '@/lib/formatters';
export default function OrderDetail({ order }: { order: any }) {
    return (
        <>
            <Head title={order.order_number} />
            <div className="space-y-6 p-4 md:p-6">
                <PageHeader
                    title={order.order_number}
                    description="Detail dan riwayat operasional pesanan."
                />
                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Pesanan</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-2">
                            <p>{order.customer_name_snapshot}</p>
                            <p className="text-sm text-muted-foreground">
                                {order.customer_phone_snapshot}
                            </p>
                            <StatusBadge status={order.status} />
                            <p className="pt-3 text-lg font-semibold">
                                {formatRupiah(Number(order.grand_total))}
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle>Item tambahan</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-2">
                            {order.additional_items.map((item: any) => (
                                <div
                                    className="flex justify-between"
                                    key={item.id}
                                >
                                    <span>
                                        {item.item_name_snapshot} ×{' '}
                                        {item.quantity}
                                    </span>
                                    <span>
                                        {formatRupiah(Number(item.subtotal))}
                                    </span>
                                </div>
                            ))}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}
