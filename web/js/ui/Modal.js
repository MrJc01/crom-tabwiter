
const Modal = ({ title, message, actionLabel, onConfirm, onClose, singleButton }) => (
    <div className="fixed inset-0 z-[70] flex items-center justify-center bg-black/40 backdrop-blur-sm animate-enter p-4">
        <div className="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 border border-stone-200">
            <h3 className="text-lg font-bold text-stone-900 mb-2">{title}</h3>
            <p className="text-sm text-stone-600 mb-6 leading-relaxed">{message}</p>
            <div className="flex justify-end gap-3">
                {!singleButton && <Button onClick={onClose}>Cancelar</Button>}
                <Button primary onClick={onConfirm}>{actionLabel || "Confirmar"}</Button>
            </div>
        </div>
    </div>
);
window.Modal = Modal;
