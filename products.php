<?php
// ============================================================================
// FILE: products.php - Products & Pricing Page
// ============================================================================
require_once 'includes/header.php';
$products = getProducts(true);
?>

<section class="page-header" style="background: var(--primary-blue); color: var(--white); padding: 3rem 2rem; text-align: center;">
    <div class="container">
        <h1>Our Products</h1>
        <p>Eco-friendly building materials made from recycled plastic waste</p>
    </div>
</section>

<section class="container">
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
        <div class="product-card fade-in">
            <?php if ($product['image']): ?>
            <img src="<?php echo UPLOAD_URL . $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
            <?php else: ?>
            <div class="product-image" style="background: var(--light-grey); display: flex; align-items: center; justify-content: center;">
                <span style="color: var(--text-dark);">No Image Available</span>
            </div>
            <?php endif; ?>
            
            <div class="product-content">
                <h3><a href="product-details.php?id=<?php echo $product['id']; ?>" style="color: inherit; text-decoration: none;"><?php echo htmlspecialchars($product['name']); ?></a></h3>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <div class="product-price">K<?php echo number_format($product['price'], 2); ?> <span style="font-size: 0.9rem; color: var(--text-dark);"><?php echo htmlspecialchars($product['unit']); ?></span></div>
                
                <?php if ($product['features']): ?>
                <ul class="product-features">
                    <?php foreach (explode('|', $product['features']) as $feature): ?>
                    <li><?php echo htmlspecialchars($feature); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                
                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button class="btn btn-primary calculate-btn" data-product-id="<?php echo $product['id']; ?>" data-product-name="<?php echo htmlspecialchars($product['name']); ?>" data-product-price="<?php echo $product['price']; ?>"><i class="fas fa-calculator"></i> Calculate Cost</button>
                    <a href="https://wa.me/<?php echo getSetting('whatsapp', '260770377471'); ?>?text=I'm interested in <?php echo urlencode($product['name']); ?>" class="btn btn-secondary" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Calculator Modal -->
<div id="calculatorModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Cost Calculator</h2>
        <p id="selectedProduct" style="color: var(--primary-blue); font-weight: 600; margin-bottom: 1.5rem;"></p>
        
        <div class="calculator">
            <div class="calc-input">
                <label for="area">Area (Square Meters):</label>
                <input type="number" id="area" placeholder="Enter area in sqm" min="1" step="0.01">
            </div>
            
            <div class="calc-input">
                <div class="toggle-switch">
                    <label>
                        <input type="checkbox" id="installation">
                        Include Installation (K5 per unit)
                    </label>
                </div>
            </div>
            
            <button class="btn btn-primary" id="calculateBtn">Calculate</button>
            
            <div class="calc-result" id="calcResult" style="display: none;">
                <h3>Estimate</h3>
                <p><strong>Area:</strong> <span id="resultArea"></span> sqm</p>
                <p><strong>Units Needed:</strong> <span id="resultUnits"></span></p>
                <p><strong>Product Cost:</strong> K<span id="resultProductCost"></span></p>
                <p id="installationCostRow" style="display: none;"><strong>Installation Cost:</strong> K<span id="resultInstallCost"></span></p>
                <p style="font-size: 1.3rem; color: var(--primary-orange); font-weight: 700; margin-top: 1rem;">
                    <strong>Total:</strong> K<span id="resultTotal"></span>
                </p>
                
                <div style="margin-top: 1.5rem;">
                    <button class="btn btn-primary" id="requestQuoteBtn">Request Official Quote</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quote Request Modal -->
<div id="quoteModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Request Quote</h2>
        
        <form id="quoteForm" class="contact-form">
            <input type="hidden" id="quoteProductId" name="product_id">
            <input type="hidden" id="quoteArea" name="area">
            <input type="hidden" id="quoteInstallation" name="installation">
            
            <div class="form-group">
                <label for="quoteName">Full Name *</label>
                <input type="text" id="quoteName" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="quoteEmail">Email Address *</label>
                <input type="email" id="quoteEmail" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="quotePhone">Phone Number *</label>
                <input type="tel" id="quotePhone" name="phone" required>
            </div>
            
            <div class="form-group">
                <label for="quoteMessage">Additional Details</label>
                <textarea id="quoteMessage" name="message" rows="4" placeholder="Tell us more about your project..."></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Submit Quote Request</button>
        </form>
        
        <div id="quoteSuccess" style="display: none; text-align: center; padding: 2rem;">
            <div style="font-size: 3rem; color: var(--primary-orange); margin-bottom: 1rem;">✓</div>
            <h3>Quote Request Submitted!</h3>
            <p>Thank you! We'll get back to you within 24 hours.</p>
        </div>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: var(--white);
    margin: 5% auto;
    padding: 2rem;
    border-radius: 10px;
    width: 90%;
    max-width: 600px;
    position: relative;
    max-height: 85vh;
    overflow-y: auto;
}

.close {
    color: var(--text-dark);
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 20px;
}

.close:hover {
    color: var(--primary-orange);
}

.calculate-btn {
    width: 100%;
}
</style>

<script>
// Product Calculator
let selectedProduct = {
    id: null,
    name: '',
    price: 0
};

// Modal handling
const calculatorModal = document.getElementById('calculatorModal');
const quoteModal = document.getElementById('quoteModal');
const closeBtns = document.getElementsByClassName('close');

// Calculate buttons
document.querySelectorAll('.calculate-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        selectedProduct.id = this.dataset.productId;
        selectedProduct.name = this.dataset.productName;
        selectedProduct.price = parseFloat(this.dataset.productPrice);
        
        document.getElementById('selectedProduct').textContent = selectedProduct.name;
        document.getElementById('calcResult').style.display = 'none';
        document.getElementById('area').value = '';
        document.getElementById('installation').checked = false;
        
        calculatorModal.style.display = 'block';
    });
});

// Close modals
Array.from(closeBtns).forEach(btn => {
    btn.onclick = function() {
        calculatorModal.style.display = 'none';
        quoteModal.style.display = 'none';
    }
});

window.onclick = function(event) {
    if (event.target == calculatorModal) {
        calculatorModal.style.display = 'none';
    }
    if (event.target == quoteModal) {
        quoteModal.style.display = 'none';
    }
}

// Calculate button
document.getElementById('calculateBtn').addEventListener('click', function() {
    const area = parseFloat(document.getElementById('area').value);
    const includeInstallation = document.getElementById('installation').checked;
    
    if (!area || area <= 0) {
        alert('Please enter a valid area');
        return;
    }
    
    // Assuming 200mm x 200mm pavers = 0.04 sqm per unit
    const unitsPerSqm = 25; // 1 / 0.04
    const unitsNeeded = Math.ceil(area * unitsPerSqm);
    const productCost = unitsNeeded * selectedProduct.price;
    const installationRate = 5; // K5 per unit
    const installationCost = includeInstallation ? unitsNeeded * installationRate : 0;
    const totalCost = productCost + installationCost;
    
    // Display results
    document.getElementById('resultArea').textContent = area.toFixed(2);
    document.getElementById('resultUnits').textContent = unitsNeeded.toLocaleString();
    document.getElementById('resultProductCost').textContent = productCost.toFixed(2);
    document.getElementById('resultInstallCost').textContent = installationCost.toFixed(2);
    document.getElementById('resultTotal').textContent = totalCost.toFixed(2);
    
    if (includeInstallation) {
        document.getElementById('installationCostRow').style.display = 'block';
    } else {
        document.getElementById('installationCostRow').style.display = 'none';
    }
    
    document.getElementById('calcResult').style.display = 'block';
    
    // Store values for quote form
    document.getElementById('quoteProductId').value = selectedProduct.id;
    document.getElementById('quoteArea').value = area;
    document.getElementById('quoteInstallation').value = includeInstallation ? '1' : '0';
});

// Request Quote button
document.getElementById('requestQuoteBtn').addEventListener('click', function() {
    calculatorModal.style.display = 'none';
    quoteModal.style.display = 'block';
});

// Quote form submission
document.getElementById('quoteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?php echo SITE_URL; ?>/api/quote.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('quoteForm').style.display = 'none';
            document.getElementById('quoteSuccess').style.display = 'block';
            
            setTimeout(() => {
                quoteModal.style.display = 'none';
                document.getElementById('quoteForm').style.display = 'block';
                document.getElementById('quoteSuccess').style.display = 'none';
                document.getElementById('quoteForm').reset();
            }, 3000);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>