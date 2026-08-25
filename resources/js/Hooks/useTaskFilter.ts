import { useCallback } from "react";
import { router } from "@inertiajs/react";
import { TaskStatus } from "@/types/task";

export function useTaskFilter(currentStatus: TaskStatus | null) {
    const setStatusFilter = useCallback((status: TaskStatus | null) => {
        router.get(
            "/tasks",
            {
                status: status ?? undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,

                only: ["tasks", "filters"],

                replace: true,
            },
        );
    }, []);

    return {
        currentStatus,
        setStatusFilter,
    };
}
