import { useForm } from "@inertiajs/react";
import { FormEvent, useRef } from "react";

export default function TaskImportForm() {
    const fileInput = useRef<HTMLInputElement>(null);
    const { setData, post, processing, errors, reset } = useForm<{
        file: File | null;
    }>({
        file: null,
    });

    const submit = (e: FormEvent) => {
        e.preventDefault();
        post("/imports", {
            forceFormData: true,
            onSuccess: () => {
                reset();
                if (fileInput.current) fileInput.current.value = "";
            },
        });
    };

    return (
        <form
            onSubmit={submit}
            className="flex items-center gap-3 rounded-lg border border-dashed border-slate-300 p-4"
        >
            <input
                ref={fileInput}
                type="file"
                accept=".csv,text/csv"
                onChange={(e) => setData("file", e.target.files?.[0] ?? null)}
                className="text-sm text-slate-600"
            />
            <button
                type="submit"
                disabled={processing}
                className="rounded-md bg-slate-800 px-4 py-2 text-sm text-white disabled:opacity-50"
            >
                {processing ? "Wysyłanie..." : "Importuj CSV"}
            </button>
            {errors.file && (
                <p className="text-sm text-rose-600">{errors.file}</p>
            )}
        </form>
    );
}
