import { useEffect, useRef } from "react";
import { router } from "@inertiajs/react";
import { ImportResult } from "@/types/import";

export function useImportPolling(
    importResult: ImportResult,
    intervalMs = 2000,
) {
    const timerRef = useRef<ReturnType<typeof setInterval> | null>(null);

    useEffect(() => {
        if (importResult.status === "completed") {
            if (timerRef.current) clearInterval(timerRef.current);
            return;
        }

        timerRef.current = setInterval(() => {
            router.reload({ only: ["importResult"] });
        }, intervalMs);

        return () => {
            if (timerRef.current) clearInterval(timerRef.current);
        };
    }, [importResult.status, intervalMs]);
}
