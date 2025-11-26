/**
 * TechTrack Product Rating System
 * Modern, interactive star rating with reviews
 * Version: 1.0.0
 */

class ProductRatingSystem {
    constructor(productId, siteUrl) {
        this.productId = productId;
        // Remove trailing slash from siteUrl
        this.siteUrl = siteUrl.replace(/\/$/, '');
        this.currentRating = 0;
        this.userRating = null;
        this.reviews = [];
        this.init();
    }

    /**
     * Initialize the rating system
     */
    async init() {
        console.log('Initializing rating system for product:', this.productId);
        console.log('Site URL:', this.siteUrl);
        
        // Load rating stats and user's rating
        await Promise.all([
            this.loadRatingStats(),
            this.loadUserRating(),
            this.loadReviews()
        ]);
        
        // Setup event listeners
        this.setupStarInteractions();
        this.setupReviewForm();
    }

    /**
     * Load product rating statistics
     */
    async loadRatingStats() {
        try {
            const response = await fetch(`${this.siteUrl}/api/ratings/stats/${this.productId}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderRatingStats(data.data);
            }
        } catch (error) {
            console.error('Failed to load rating stats:', error);
        }
    }

    /**
     * Load user's existing rating
     */
    async loadUserRating() {
        try {
            const response = await fetch(`${this.siteUrl}/api/ratings/my-rating/${this.productId}`);
            const data = await response.json();
            
            if (data.success && data.has_rating) {
                this.userRating = data.data;
                this.renderUserRating(data.data);
            }
        } catch (error) {
            console.error('Failed to load user rating:', error);
        }
    }

    /**
     * Load product reviews
     */
    async loadReviews(sort = 'recent') {
        try {
            const response = await fetch(`${this.siteUrl}/api/ratings/reviews/${this.productId}?sort=${sort}&limit=20`);
            const data = await response.json();
            
            if (data.success) {
                this.reviews = data.data;
                this.renderReviews();
            }
        } catch (error) {
            console.error('Failed to load reviews:', error);
        }
    }

    /**
     * Render rating statistics with star display and distribution
     */
    renderRatingStats(stats) {
        const avgRating = parseFloat(stats.average_rating);
        const totalRatings = stats.total_ratings;
        
        // Update summary
        document.getElementById('rating-score').textContent = avgRating.toFixed(1);
        document.getElementById('rating-count').textContent = `${totalRatings} ${totalRatings === 1 ? 'rating' : 'ratings'}`;
        
        // Render stars
        this.renderStars('stars-display', avgRating, false);
        
        // Render distribution
        const distributionHTML = [5, 4, 3, 2, 1].map(star => {
            const count = stats.distribution[star] || 0;
            const percentage = stats.percentages[star] || 0;
            
            return `
                <div class="distribution-row">
                    <div class="distribution-star">
                        ${star} 
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#F59E0B">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <div class="distribution-bar">
                        <div class="distribution-fill" style="width: ${percentage}%"></div>
                    </div>
                    <div class="distribution-count">${count}</div>
                </div>
            `;
        }).join('');
        
        const distributionContainer = document.getElementById('rating-distribution');
        if (distributionContainer) {
            distributionContainer.innerHTML = distributionHTML;
        }
    }

    /**
     * Render user's current rating
     */
    renderUserRating(ratingData) {
        const container = document.getElementById('star-rating-interactive');
        if (!container) return;
        
        const rating = parseInt(ratingData.rating);
        
        container.innerHTML = `
            <div class="rating-submitted">
                <svg class="rating-submitted-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
                <div class="rating-submitted-text">
                    Your rating: ${rating} ${rating === 1 ? 'star' : 'stars'}
                </div>
                <button class="rating-edit-btn" onclick="productRating.editRating()">
                    Edit
                </button>
            </div>
        `;
        
        this.currentRating = rating;
    }

    /**
     * Render interactive stars
     */
    renderStars(containerId, rating, interactive = false) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        const fullStars = Math.floor(rating);
        const hasHalf = rating % 1 >= 0.5;
        const emptyStars = 5 - fullStars - (hasHalf ? 1 : 0);
        
        let html = '';
        
        // Full stars
        for (let i = 0; i < fullStars; i++) {
            html += this.getStarSVG('filled', i + 1, interactive);
        }
        
        // Half star
        if (hasHalf) {
            html += this.getStarSVG('half', fullStars + 1, interactive);
        }
        
        // Empty stars
        for (let i = 0; i < emptyStars; i++) {
            html += this.getStarSVG('empty', fullStars + (hasHalf ? 1 : 0) + i + 1, interactive);
        }
        
        container.innerHTML = html;
    }

    /**
     * Get star SVG HTML
     */
    getStarSVG(type, index, interactive) {
        const fillClass = type === 'filled' ? 'filled' : (type === 'half' ? 'half' : '');
        
        if (interactive) {
            return `
                <button class="star-btn" data-rating="${index}">
                    <svg class="star-icon ${fillClass}" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </button>
            `;
        } else {
            return `
                <svg class="star-icon ${fillClass}" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            `;
        }
    }

    /**
     * Setup star interaction (hover and click)
     */
    setupStarInteractions() {
        const container = document.getElementById('stars-input');
        if (!container) return;
        
        // Initial render
        this.renderStars('stars-input', 0, true);
        
        const starButtons = container.querySelectorAll('.star-btn');
        
        starButtons.forEach((btn, index) => {
            // Hover effect
            btn.addEventListener('mouseenter', () => {
                this.highlightStars(index + 1);
            });
            
            // Click to rate
            btn.addEventListener('click', () => {
                this.submitRating(index + 1);
            });
        });
        
        // Reset on mouse leave
        container.addEventListener('mouseleave', () => {
            this.highlightStars(this.currentRating);
        });
    }

    /**
     * Highlight stars up to a certain rating
     */
    highlightStars(rating) {
        const starButtons = document.querySelectorAll('#stars-input .star-btn');
        
        starButtons.forEach((btn, index) => {
            if (index < rating) {
                btn.classList.add('hovered');
            } else {
                btn.classList.remove('hovered');
            }
        });
    }

    /**
     * Submit rating to API
     */
    async submitRating(rating) {
        try {
            // Visual feedback
            const starButtons = document.querySelectorAll('#stars-input .star-btn');
            starButtons.forEach((btn, index) => {
                if (index < rating) {
                    btn.classList.add('selected');
                }
            });
            
            const url = `${this.siteUrl}/api/ratings/submit`;
            console.log('Submitting rating to:', url);
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: this.productId,
                    rating: rating
                })
            });
            
            console.log('Response status:', response.status);
            const responseText = await response.text();
            console.log('Response text:', responseText.substring(0, 200));
            
            const data = JSON.parse(responseText);
            
            if (data.success) {
                this.currentRating = rating;
                this.userRating = data;
                
                // Show success message
                this.showMessage(data.message, 'success');
                
                // Update UI
                await this.loadRatingStats();
                this.renderUserRating({ rating: rating, id: data.rating_id });
                
                // Show review form
                this.showReviewForm(data.rating_id);
            } else {
                this.showMessage(data.message, 'error');
            }
        } catch (error) {
            console.error('Failed to submit rating:', error);
            this.showMessage('Failed to submit rating. Please try again.', 'error');
        }
    }

    /**
     * Enable editing of existing rating
     */
    editRating() {
        const container = document.getElementById('star-rating-interactive');
        if (!container) return;
        
        container.innerHTML = `
            <div class="star-rating-label">Rate this product:</div>
            <div id="stars-input" class="stars-input"></div>
        `;
        
        this.setupStarInteractions();
        this.highlightStars(this.currentRating);
    }

    /**
     * Show review form
     */
    showReviewForm(ratingId) {
        const form = document.getElementById('write-review-form');
        if (!form) return;
        
        form.classList.add('active');
        form.style.display = 'block';
        form.dataset.ratingId = ratingId;
        
        // Scroll to form
        form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /**
     * Setup review form submission
     */
    setupReviewForm() {
        const form = document.getElementById('review-form');
        if (!form) return;
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.submitReview();
        });
        
        // Sort dropdown
        const sortSelect = document.getElementById('review-sort');
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                this.loadReviews(e.target.value);
            });
        }
    }

    /**
     * Submit review
     */
    async submitReview() {
        const form = document.getElementById('review-form');
        const ratingId = document.getElementById('write-review-form').dataset.ratingId;
        
        const reviewData = {
            product_id: this.productId,
            rating_id: parseInt(ratingId),
            customer_name: document.getElementById('reviewer-name').value,
            review_title: document.getElementById('review-title').value,
            review_text: document.getElementById('review-text').value
        };
        
        try {
            const response = await fetch(`${this.siteUrl}/api/ratings/review`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(reviewData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showMessage(data.message, 'success');
                form.reset();
                document.getElementById('write-review-form').style.display = 'none';
                await this.loadReviews();
            } else {
                this.showMessage(data.message, 'error');
            }
        } catch (error) {
            console.error('Failed to submit review:', error);
            this.showMessage('Failed to submit review. Please try again.', 'error');
        }
    }

    /**
     * Render reviews list
     */
    renderReviews() {
        const container = document.getElementById('reviews-list');
        if (!container) return;
        
        if (this.reviews.length === 0) {
            container.innerHTML = `
                <div class="no-reviews">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    <p>No reviews yet. Be the first to review this product!</p>
                </div>
            `;
            return;
        }
        
        const reviewsHTML = this.reviews.map(review => this.renderReviewCard(review)).join('');
        container.innerHTML = reviewsHTML;
    }

    /**
     * Render individual review card
     */
    renderReviewCard(review) {
        const initial = review.customer_name ? review.customer_name.charAt(0).toUpperCase() : '?';
        const rating = parseInt(review.rating);
        const date = new Date(review.created_at).toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        
        const starsHTML = Array(5).fill(0).map((_, i) => {
            const filled = i < rating;
            return `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="${filled ? '#F59E0B' : '#E5E7EB'}">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            `;
        }).join('');
        
        const verifiedBadge = review.verified_purchase ? `
            <span class="verified-badge">
                <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Verified Purchase
            </span>
        ` : '';
        
        return `
            <div class="review-card">
                <div class="review-header-info">
                    <div class="review-author">
                        <div class="review-avatar">${initial}</div>
                        <div class="review-author-details">
                            <div class="review-author-name">
                                ${this.escapeHtml(review.customer_name)}
                                ${verifiedBadge}
                            </div>
                            <div class="review-date">${date}</div>
                        </div>
                    </div>
                    <div class="review-rating-stars">${starsHTML}</div>
                </div>
                ${review.review_title ? `<div class="review-title">${this.escapeHtml(review.review_title)}</div>` : ''}
                ${review.review_text ? `<div class="review-text">${this.escapeHtml(review.review_text)}</div>` : ''}
                <div class="review-helpful">
                    <button class="helpful-btn" onclick="productRating.markHelpful(${review.id})">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                        </svg>
                        Helpful (${review.helpful_count || 0})
                    </button>
                </div>
            </div>
        `;
    }

    /**
     * Mark review as helpful
     */
    async markHelpful(reviewId) {
        try {
            const response = await fetch(`${this.siteUrl}/api/ratings/helpful/${reviewId}`, {
                method: 'POST'
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showMessage('Thank you for your feedback!', 'success');
                await this.loadReviews();
            }
        } catch (error) {
            console.error('Failed to mark as helpful:', error);
        }
    }

    /**
     * Show message notification
     */
    showMessage(message, type = 'success') {
        const messageDiv = document.createElement('div');
        messageDiv.className = `rating-message rating-message-${type}`;
        messageDiv.textContent = message;
        messageDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            background: ${type === 'success' ? '#D1FAE5' : '#FEE2E2'};
            color: ${type === 'success' ? '#065F46' : '#991B1B'};
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            font-weight: 600;
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(messageDiv);
        
        setTimeout(() => {
            messageDiv.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => messageDiv.remove(), 300);
        }, 3000);
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const productIdElement = document.getElementById('product-id');
    const siteUrlElement = document.getElementById('site-url');
    
    if (productIdElement && siteUrlElement) {
        window.productRating = new ProductRatingSystem(
            productIdElement.value,
            siteUrlElement.value
        );
    }
});
