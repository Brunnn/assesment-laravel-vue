export function useDate() {
    function ToDate(date?: string | Date | null): string | null {
        if (!date) return null;
        return new Date(date).toLocaleString("en-US");
    }

    return {
        ToDate,
    };
}
