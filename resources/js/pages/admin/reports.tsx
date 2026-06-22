import { Head } from '@inertiajs/react';
import { ReportsPageContent } from '@/components/admin/reports-page-content';
import type { ComponentProps } from 'react';

export default function Reports(props: ComponentProps<typeof ReportsPageContent>) { return <> <Head title="Laporan & Apriori" /><ReportsPageContent {...props} /></>; }