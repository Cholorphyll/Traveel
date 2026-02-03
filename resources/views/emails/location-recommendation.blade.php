<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore {{ $location['name'] }}</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin: 30px 0 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .item-card {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        .item-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin: 0 0 8px;
        }
        .item-details {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }
        .rating {
            color: #f59e0b;
            font-size: 14px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #666;
        }
        .footer a {
            color: #667eea;
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
            <h1>🌍 Explore {{ $location['name'] }}</h1>
            <p>Personalized recommendations just for you</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hi {{ $user->name }},
            </div>
            
            <div class="intro">
                We noticed you were interested in <strong>{{ $location['name'] }}</strong>. We've handpicked some amazing places and experiences to make your visit unforgettable!
            </div>

            @if(!empty($hotels) && count($hotels) > 0)
            <div class="section-title">🏨 Top Hotels in {{ $location['name'] }}</div>
            @foreach(array_slice($hotels, 0, 3) as $hotel)
            <div class="item-card">
                <div class="item-title">{{ $hotel['name'] ?? $hotel['Title'] ?? 'Hotel' }}</div>
                @if(isset($hotel['rating']) || isset($hotel['Averagerating']))
                <div class="rating">
                    ⭐ {{ number_format($hotel['rating'] ?? $hotel['Averagerating'] ?? 0, 1) }}/5
                    @if(isset($hotel['review_count']) || isset($hotel['ReviewCount']))
                    ({{ $hotel['review_count'] ?? $hotel['ReviewCount'] }} reviews)
                    @endif
                </div>
                @endif
                @if(isset($hotel['address']) || isset($hotel['Address']))
                <div class="item-details">📍 {{ $hotel['address'] ?? $hotel['Address'] }}</div>
                @endif
            </div>
            @endforeach
            @endif

            @if(!empty($attractions) && count($attractions) > 0)
            <div class="section-title">🎯 Must-See Attractions</div>
            @foreach(array_slice($attractions, 0, 4) as $attraction)
            <div class="item-card">
                <div class="item-title">{{ $attraction['Title'] ?? $attraction['name'] ?? 'Attraction' }}</div>
                @if(isset($attraction['Averagerating']) || isset($attraction['rating']))
                <div class="rating">
                    ⭐ {{ number_format($attraction['Averagerating'] ?? $attraction['rating'] ?? 0, 1) }}/5
                </div>
                @endif
                @if(isset($attraction['MicroSummary']))
                <div class="item-details">{{ Str::limit($attraction['MicroSummary'], 100) }}</div>
                @endif
            </div>
            @endforeach
            @endif

            @if(!empty($restaurants) && count($restaurants) > 0)
            <div class="section-title">🍽️ Top Restaurants</div>
            @foreach(array_slice($restaurants, 0, 3) as $restaurant)
            <div class="item-card">
                <div class="item-title">{{ $restaurant['Title'] ?? $restaurant['name'] ?? 'Restaurant' }}</div>
                @if(isset($restaurant['Averagerating']) || isset($restaurant['rating']))
                <div class="rating">
                    ⭐ {{ number_format($restaurant['Averagerating'] ?? $restaurant['rating'] ?? 0, 1) }}/5
                </div>
                @endif
                @if(isset($restaurant['cuisines']))
                <div class="item-details">🍴 {{ $restaurant['cuisines'] }}</div>
                @endif
                @if(isset($restaurant['Cost']))
                <div class="item-details">💰 {{ $restaurant['Cost'] }}</div>
                @endif
            </div>
            @endforeach
            @endif

            <center>
                <a href="{{ url('/lo-' . ($location['slug'] ?? $location['Slug'] ?? '')) }}" class="cta-button">
                    Explore {{ $location['name'] }} Now
                </a>
            </center>

            <div style="margin-top: 30px; padding: 20px; background: #f0f9ff; border-radius: 8px; border-left: 4px solid #3b82f6;">
                <strong style="color: #1e40af;">💡 Pro Tip:</strong>
                <p style="margin: 10px 0 0; color: #1e3a8a; font-size: 14px;">
                    Book your hotels in advance to get the best deals, and don't miss out on local experiences that make {{ $location['name'] }} special!
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
