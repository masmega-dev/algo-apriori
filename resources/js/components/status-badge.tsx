import { Badge } from '@/components/ui/badge';
import { formatOrderStatus } from '@/lib/formatters';
import type { OrderStatus } from '@/types/cake-shop';

const variants: Record<OrderStatus, 'default' | 'secondary' | 'destructive'> = {
    pending: 'secondary',
    completed: 'default',
    cancelled: 'destructive',
};

export function StatusBadge({ status }: { status: OrderStatus }) {
    return <Badge variant={variants[status]}>{formatOrderStatus(status)}</Badge>;
}
