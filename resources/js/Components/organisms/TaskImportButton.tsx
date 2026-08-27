import { useForm } from "@inertiajs/react";
import { FormEvent, useRef } from "react";

export default function TaskImportButton() {
    const fileInput = useRef<HTMLInputElement>(null);
    const { setData, post, processing, errors } = useForm<{
        file: File | null;
    }>({
        file: null,
    });

    const handlePick = () => fileInput.current?.click();

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0] ?? null;
        setData("file", file);
        if (file) {
            post("/imports", { forceFormData: true });
        }
    };

    return (
        <div className="ml-auto flex flex-col items-end">
            <button
                type="button"
                onClick={handlePick}
                disabled={processing}
                className="rounded-md bg-slate-800 px-4 py-2 text-sm text-white"
            >
                {processing ? "Wysyłanie..." : "Importuj CSV"}
            </button>
            <input
                ref={fileInput}
                type="file"
                accept=".csv,text/csv"
                onChange={handleFileChange}
                className="hidden"
            />
            {errors.file && (
                <p className="mt-1 text-xs text-rose-600">{errors.file}</p>
            )}
        </div>
    );
}
