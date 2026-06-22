import {
    CalendarClock,
    CircleDollarSign,
    ClipboardList,
    TrendingUp,
} from 'lucide-react';
import { PageHeader } from '@/components/page-header';
import { StatusBadge } from '@/components/status-badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { recentOrders } from '@/lib/cake-shop-fixtures';
import { formatRupiah } from '@/lib/formatters';

const stats = [
    { label: 'Order hari ini', value: '12', icon: ClipboardList },
    { label: 'Pickup hari ini', value: '7', icon: CalendarClock },
    {
        label: 'Omzet bulan ini',
        value: formatRupiah(4850000),
        icon: CircleDollarSign,
    },
    { label: 'Pertumbuhan order', value: '+18%', icon: TrendingUp },
];

export function DashboardOverview() {
    return (
        <div className="space-y-6 p-4 md:p-6">
            <PageHeader
                title="Dashboard"
                description="Pantau pesanan, jadwal pickup, dan performa toko hari ini."
            />
            <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                {stats.map(({ label, value, icon: Icon }) => (
                    <Card key={label} className="border-border/70 shadow-sm">
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium text-muted-foreground">
                                {label}
                            </CardTitle>
                            <Icon className="size-4 text-primary" />
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-semibold">{value}</p>
                        </CardContent>
                    </Card>
                ))}
            </div>
            <div className="grid gap-6 xl:grid-cols-[1.5fr_1fr]">
                <Card>
                    <CardHeader>
                        <CardTitle>Order terbaru</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-3">
                        {recentOrders.map((order) => (
                            <div
                                key={order.id}
                                className="flex items-center justify-between gap-3 rounded-lg border border-border/70 p-3"
                            >
                                <div>
                                    <p className="font-medium">
                                        {order.customerName}
                                    </p>
                                    <p className="text-sm text-muted-foreground">
                                        {order.id} · {order.pickupAt}
                                    </p>
                                </div>
                                <div className="text-right">
                                    <p className="mb-1 font-medium">
                                        {formatRupiah(order.total)}
                                    </p>
                                    <StatusBadge status={order.status} />
                                </div>
                            </div>
                        ))}
                    </CardContent>
                </Card>
                <Card className="bg-gradient-to-br from-blue-600 to-sky-500 text-white">
                    <CardHeader>
                        <CardTitle className="text-white">
                            Perlu diproses
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p className="text-4xl font-semibold">7</p>
                        <p className="mt-2 text-sm text-blue-100">
                            Pesanan memiliki pickup hari ini. Prioritaskan
                            konfirmasi dan persiapan produksi.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>
    );
}
