import { TextareaHTMLAttributes } from "react";

interface TextareaProps extends TextareaHTMLAttributes<HTMLTextAreaElement> {
    className?: string;
}

export default function Textarea({ className = "", ...props }: TextareaProps) {
    return (
        <textarea
            className={`
                w-full
                rounded-md
                border border-slate-300
                px-3 py-2
                text-sm
                text-slate-700
                placeholder:text-slate-400
                focus:outline-none
                focus:ring-2
                focus:ring-slate-400
                ${className}
            `}
            {...props}
        />
    );
}
