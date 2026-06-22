import { Head } from '@inertiajs/react';
import { BarChart3, Download } from 'lucide-react';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const rules = [
    ['Pisau', 'Piring', '42%', '77%', '1.42'],
    ['Lilin', 'Pisau', '31%', '68%', '1.23'],
    ['Topper', 'Lilin', '18%', '64%', '1.18'],
];
export default function Reports() {
    return (
        <>
            <Head title="Laporan & Apriori" />
            <div className="space-y-6 p-4 md:p-6">
                <PageHeader
                    title="Laporan & Apriori"
                    description="Gunakan pola pembelian item tambahan untuk rekomendasi paket."
                    actions={
                        <Button variant="outline">
                            <Download className="size-4" />
                            Export CSV
                        </Button>
                    }
                />
                <div className="grid gap-4 md:grid-cols-3">
                    <Metric label="Total order" value="128" />
                    <Metric label="Order selesai" value="96" />
                    <Metric label="Omzet selesai" value="Rp18.450.000" />
                </div>
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-2">
                            <BarChart3 className="size-5 text-primary" />
                            <CardTitle>Aturan asosiasi</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent className="overflow-x-auto">
                        <table className="w-full min-w-[620px] text-sm">
                            <thead className="text-left text-muted-foreground">
                                <tr>
                                    <th>Jika membeli</th>
                                    <th>Juga membeli</th>
                                    <th>Support</th>
                                    <th>Confidence</th>
                                    <th>Lift</th>
                                </tr>
                            </thead>
                            <tbody>
                                {rules.map((rule) => (
                                    <tr key={rule[0]} className="border-t">
                                        <td className="py-3 font-medium">
                                            {rule[0]}
                                        </td>
                                        <td>{rule[1]}</td>
                                        <td>{rule[2]}</td>
                                        <td>{rule[3]}</td>
                                        <td>{rule[4]}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
function Metric({ label, value }: { label: string; value: string }) {
    return (
        <Card>
            <CardHeader className="pb-2">
                <CardTitle className="text-sm font-medium text-muted-foreground">
                    {label}
                </CardTitle>
            </CardHeader>
            <CardContent>
                <p className="text-2xl font-semibold">{value}</p>
            </CardContent>
        </Card>
    );
}
