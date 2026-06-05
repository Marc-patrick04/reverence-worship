<div>
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Term Structure Settings</h3>
       
    <div class="bg-white rounded-xl shadow-md p-6">
        <form id="financeSettingsForm" method="POST">
            @csrf
            
            <!-- Current Year -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Year</label>
                <select name="current_year" id="currentYear" class="w-48 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="" selected>Select Year</option>
                    <option value="2026">2026</option>
                    <option value="2027">2027</option>
                    <option value="2028">2028</option>
                    <option value="2029">2029</option>
                    <option value="2030">2030</option>
                </select>
            </div>
            
            <!-- Number of Terms - Input field instead of dropdown -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Terms</label>
                <div class="flex items-center gap-4">
                    <input type="number" id="numberOfTerms" min="1" max="12" step="1" value="3"
                           class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="button" onclick="updateTermsCount()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                        apply
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">Enter number of terms (1-12). Percentages will be distributed evenly.</p>
            </div>
            
            <!-- Dynamic Terms Container -->
            <div id="termsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <!-- Terms will be dynamically added here -->
            </div>
            
            <!-- Total Percentage -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">Total Percentage</span>
                    <span id="totalPercentage" class="text-2xl font-bold text-blue-600">100.00%</span>
                </div>
                <p id="percentageWarning" class="text-xs text-red-500 mt-1 hidden">Must equal 100%</p>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div id="progressBar" class="bg-blue-600 h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>
            
            <!-- Equal Distribution Button -->
            <div class="mb-6">
                <button type="button" onclick="distributeEvenly()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-calculator mr-2"></i> Distribute Evenly
                </button>
                <span class="text-xs text-gray-500 ml-3">Reset all term percentages to equal distribution</span>
            </div>
            
            <!-- Messages -->
            <div id="successMessage" class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg hidden">
                <i class="fas fa-check-circle mr-2"></i> Settings saved successfully!
            </div>
            <div id="errorMessage" class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg hidden">
                <i class="fas fa-exclamation-circle mr-2"></i> <span id="errorText"></span>
            </div>
            
            <!-- Save Button -->
            <div class="flex justify-end">
                <button type="submit" id="saveButton" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                    <i class="fas fa-save mr-2"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentTermsCount = 3;

function distributeEvenly() {
    const numTerms = currentTermsCount;
    const equalPercent = (100 / numTerms).toFixed(2);
    const remainder = 100 - (equalPercent * numTerms);
    
    for (let i = 1; i <= numTerms; i++) {
        let percent = parseFloat(equalPercent);
        // Add remainder to first term if any
        if (i === 1 && remainder > 0) {
            percent = parseFloat(equalPercent) + parseFloat(remainder.toFixed(2));
        }
        const input = document.getElementById(`term${i}Percentage`);
        if (input) {
            input.value = percent.toFixed(2);
        }
    }
    updateTotalPercentage();
}

function updateTermsCount() {
    let newCount = parseInt(document.getElementById('numberOfTerms').value);
    
    if (isNaN(newCount) || newCount < 1) {
        newCount = 1;
    }
    if (newCount > 12) {
        newCount = 12;
        document.getElementById('numberOfTerms').value = 12;
    }
    
    currentTermsCount = newCount;
    renderTerms();
    
    // Distribute evenly after changing term count
    setTimeout(() => {
        distributeEvenly();
    }, 100);
}

function renderTerms() {
    const container = document.getElementById('termsContainer');
    container.innerHTML = '';
    
    // Get saved percentages or use defaults
    const savedPercentages = window.savedPercentages || {};
    
    for (let i = 1; i <= currentTermsCount; i++) {
        let defaultValue = savedPercentages[i];
        if (!defaultValue) {
            defaultValue = (100 / currentTermsCount).toFixed(2);
        }
        
        const termDiv = document.createElement('div');
        termDiv.className = 'border rounded-lg p-4 bg-gray-50';
        termDiv.innerHTML = `
            <label class="block text-sm font-medium text-gray-700 mb-2">Term ${i} Percentage (%)</label>
            <input type="number" id="term${i}Percentage" name="term_percentages[]" 
                   value="${defaultValue}" step="0.01" 
                   class="term-percentage w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   data-term="${i}"
                   onchange="updateTotalPercentage()"
                   onkeyup="updateTotalPercentage()">
            <input type="hidden" name="term_numbers[]" value="${i}">
        `;
        container.appendChild(termDiv);
    }
    
    updateTotalPercentage();
}

function updateTotalPercentage() {
    const termInputs = document.querySelectorAll('.term-percentage');
    let total = 0;
    
    termInputs.forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    
    const totalPercent = total.toFixed(2);
    
    // Update total display
    document.getElementById('totalPercentage').textContent = totalPercent + '%';
    
    // Update progress bar
    const progressBar = document.getElementById('progressBar');
    if (total <= 100) {
        progressBar.style.width = total + '%';
        progressBar.classList.remove('bg-red-600');
        progressBar.classList.add('bg-blue-600');
    } else {
        progressBar.style.width = '100%';
        progressBar.classList.remove('bg-blue-600');
        progressBar.classList.add('bg-red-600');
    }
    
    // Show/hide warning
    const warning = document.getElementById('percentageWarning');
    const saveButton = document.getElementById('saveButton');
    
    if (Math.abs(total - 100) > 0.01) {
        warning.classList.remove('hidden');
        saveButton.disabled = true;
        saveButton.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        warning.classList.add('hidden');
        saveButton.disabled = false;
        saveButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

function showMessage(type, message) {
    if (type === 'success') {
        const successDiv = document.getElementById('successMessage');
        successDiv.classList.remove('hidden');
        setTimeout(() => {
            successDiv.classList.add('hidden');
        }, 3000);
    } else if (type === 'error') {
        const errorDiv = document.getElementById('errorMessage');
        document.getElementById('errorText').textContent = message;
        errorDiv.classList.remove('hidden');
        setTimeout(() => {
            errorDiv.classList.add('hidden');
        }, 3000);
    }
}

// Load settings from server
function loadSettings() {
    fetch('/finance/settings/get', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.settings) {
            if (data.settings.current_year) {
                document.getElementById('currentYear').value = data.settings.current_year;
            }
            if (data.settings.number_of_terms) {
                document.getElementById('numberOfTerms').value = data.settings.number_of_terms;
                currentTermsCount = data.settings.number_of_terms;
            }
            if (data.settings.term_percentages) {
                window.savedPercentages = data.settings.term_percentages;
            }
            renderTerms();
        } else {
            renderTerms();
            distributeEvenly();
        }
    })
    .catch(error => {
        console.error('Error loading settings:', error);
        renderTerms();
        distributeEvenly();
    });
}

// Save settings
document.getElementById('financeSettingsForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const termInputs = document.querySelectorAll('.term-percentage');
    let total = 0;
    const termPercentages = [];
    const termNumbers = [];
    
    termInputs.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
        termPercentages.push(value);
        termNumbers.push(parseInt(input.dataset.term));
    });
    
    if (Math.abs(total - 100) > 0.01) {
        showMessage('error', 'Total percentage must equal 100%');
        return;
    }
    
    const formData = new FormData();
    formData.append('current_year', document.getElementById('currentYear').value);
    formData.append('number_of_terms', currentTermsCount);
    formData.append('term_percentages', JSON.stringify(termPercentages));
    formData.append('term_numbers', JSON.stringify(termNumbers));
    
    const saveBtn = document.getElementById('saveButton');
    const originalText = saveBtn.innerHTML;
    
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
    saveBtn.disabled = true;
    
    fetch('/finance/settings/update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('success', 'Settings saved successfully!');
            loadSettings();
        } else {
            showMessage('error', data.message || 'Failed to save settings');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('error', 'Network error: ' + error.message);
    })
    .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadSettings();
});
</script>