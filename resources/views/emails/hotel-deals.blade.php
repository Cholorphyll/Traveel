<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Deals in {{ $location['name'] }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }
        .intro {
            font-size: 15px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .hotel-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .hotel-name {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 10px;
        }
        .hotel-rating {
            color: #f59e0b;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .hotel-details {
            font-size: 14px;
            color: #6b7280;
            margin: 5px 0;
        }
        .hotel-amenities {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }
        .amenity-tag {
            background: #f3f4f6;
            color: #4b5563;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .view-hotel-btn {
            display: inline-block;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 12px;
            font-size: 14px;
        }
        .cta-section {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin: 30px 0;
        }
        .cta-title {
            font-size: 20px;
            font-weight: 600;
            color: #92400e;
            margin: 0 0 15px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 6px;
            font-weight: 600;
            margin: 10px 0;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #666;
        }
        .footer a {
            color: #f59e0b;
            text-decoration: none;
        }
        .unsubscribe {
            margin-top: 15px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🏨 Best Hotel Deals</h1>
            <p>Exclusive offers in {{ $location['name'] }}</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hi {{ $user->name }},
            </div>
            
            <div class="intro">
                Planning a trip to <strong>{{ $location['name'] }}</strong>? We've found the best hotels with great ratings and amenities just for you!
            </div>

            @if(!empty($hotels) && count($hotels) > 0)
                @foreach(array_slice($hotels, 0, 5) as $hotel)
                <div class="hotel-card">
                    <div class="hotel-name">{{ $hotel['name'] ?? $hotel['Title'] ?? 'Hotel' }}</div>
                    
                    @if(isset($hotel['rating']) || isset($hotel['Averagerating']))
                    <div class="hotel-rating">
                        ⭐ {{ number_format($hotel['rating'] ?? $hotel['Averagerating'] ?? 0, 1) }}/5
                        @if(isset($hotel['review_count']) || isset($hotel['ReviewCount']))
                        <span style="color: #6b7280;">({{ $hotel['review_count'] ?? $hotel['ReviewCount'] }} reviews)</span>
                        @endif
                    </div>
                    @endif
                    
                    @if(isset($hotel['address']) || isset($hotel['Address']))
                    <div class="hotel-details">📍 {{ $hotel['address'] ?? $hotel['Address'] }}</div>
                    @endif
                    
                    @if(isset($hotel['star_rating']) || isset($hotel['StarRating']))
                    <div class="hotel-details">⭐ {{ $hotel['star_rating'] ?? $hotel['StarRating'] }} Star Hotel</div>
                    @endif

                    @if(isset($hotel['amenities']) && is_array($hotel['amenities']))
                    <div class="hotel-amenities">
                        @foreach(array_slice($hotel['amenities'], 0, 4) as $amenity)
                        <span class="amenity-tag">{{ $amenity }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    <a href="{{ url('/hotel/' . ($hotel['slug'] ?? $hotel['Slug'] ?? $hotel['id'] ?? '')) }}" class="view-hotel-btn">
                        View Details
                    </a>
                </div>
                @endforeach
            @else
                <p style="text-align: center; color: #6b7280; padding: 40px 20px;">
                    We're currently updating our hotel listings for {{ $location['name'] }}. Check back soon!
                </p>
            @endif

            <div class="cta-section">
                <div class="cta-title">Ready to Book Your Stay?</div>
                <p style="color: #92400e; margin: 10px 0;">Discover more hotels and exclusive deals in {{ $location['name'] }}</p>
                <a href="{{ url('/hotels/' . ($location['slug'] ?? $location['Slug'] ?? '')) }}" class="cta-button">
                    View All Hotels in {{ $location['name'] }}
                </a>
            </div>

            <div style="margin-top: 30px; padding: 20px; background: #ecfdf5; border-radius: 8px; border-left: 4px solid #10b981;">
                <strong style="color: #065f46;">💡 Booking Tip:</strong>
                <p style="margin: 10px 0 0; color: #047857; font-size: 14px;">
                    Compare prices, read recent reviews, and book directly to get the best rates. Early bookings often come with special discounts!
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>Happy travels from the Travell team! ✈️</p>
            <p>
                <a href="{{ url('/') }}">Visit our website</a> | 
                <a href="{{ url('/user/email-preferences') }}">Email Preferences</a>
            </p>
            <p class="unsubscribe">
                Don't want to receive these emails? 
                <a href="{{ url('/user/unsubscribe') }}">Unsubscribe</a>
            </p>
        </div>
    </div>
</body>
</html>
