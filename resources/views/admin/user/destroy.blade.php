    <!-- Sweet Alert Modal -->
    <div id="alertModal"
        class="fixed inset-0 flex items-center justify-center z-50 bg-black/40 backdrop-blur-sm transition-opacity duration-300 opacity-0 pointer-events-none"
        aria-hidden="true">
        <div id="alertBox"
            class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 text-center transform scale-90 opacity-0 transition-all duration-300">
            <!-- Icon -->
            <div id="alertIcon" class="text-4xl mb-4"></div>

            <!-- Title -->
            <h3 id="alertTitle" class="text-lg font-semibold text-gray-800 mb-2"></h3>

            <!-- Message -->
            <p id="alertMessage" class="text-sm text-gray-500 mb-6"></p>

            <!-- Actions -->
            <div class="flex justify-center space-x-3">
                <button onclick="closeAlert()" id="cancelButton"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                    Cancel
                </button>
                <button id="confirmButton"
                    class="px-4 py-2 text-sm rounded-lg bg-red-500 text-white hover:bg-red-600 transition">
                    Confirm
                </button>
            </div>
        </div>
    </div>


    <script>
        const modal = document.getElementById('alertModal');
        const box = document.getElementById('alertBox');
        const icon = document.getElementById('alertIcon');
        const title = document.getElementById('alertTitle');
        const message = document.getElementById('alertMessage');
        const confirmBtn = document.getElementById('confirmButton');
        const cancelBtn = document.getElementById('cancelButton');

        let confirmedAction = null;

        function openAlert({
            type = 'info',
            titleText = '',
            messageText = '',
            onConfirm = null
        }) {
            confirmedAction = onConfirm;

            icon.innerHTML = {
                success: '<i class="fas fa-check-circle text-green-500"></i>',
                error: '<i class="fas fa-times-circle text-red-500"></i>',
                warning: '<i class="fas fa-exclamation-triangle text-yellow-500"></i>',
                info: '<i class="fas fa-info-circle text-blue-500"></i>'
            } [type];

            title.innerText = titleText;
            message.innerText = messageText;

            modal.classList.remove('pointer-events-none', 'opacity-0');
            modal.setAttribute('aria-hidden', 'false');

            setTimeout(() => {
                box.classList.remove('scale-90', 'opacity-0');
                box.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeAlert() {
            box.classList.remove('scale-100', 'opacity-100');
            box.classList.add('scale-90', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('pointer-events-none', 'opacity-0');
                modal.setAttribute('aria-hidden', 'true');
            }, 300);
        }

        confirmBtn.addEventListener('click', () => {
            closeAlert();
            if (typeof confirmedAction === 'function') {
                confirmedAction();
            }
        });

        // Attach click handler to all delete buttons
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const form = button.closest('form');
                openAlert({
                    type: 'warning',
                    titleText: 'Are you sure?',
                    messageText: 'This action cannot be undone.',
                    onConfirm: () => form.submit()
                });
            });
        });
    </script>
