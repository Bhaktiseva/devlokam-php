CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(32) DEFAULT NULL,
    age INT DEFAULT NULL,
    gender VARCHAR(24) DEFAULT NULL,
    gotra VARCHAR(120) DEFAULT NULL,
    address_line VARCHAR(255) DEFAULT NULL,
    city VARCHAR(120) DEFAULT NULL,
    state VARCHAR(120) DEFAULT NULL,
    pincode VARCHAR(20) DEFAULT NULL,
    role VARCHAR(30) NOT NULL DEFAULT 'devotee',
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS temples (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    deity_primary VARCHAR(120) NOT NULL,
    deity_secondary VARCHAR(120) DEFAULT NULL,
    city VARCHAR(120) NOT NULL,
    state VARCHAR(120) NOT NULL,
    region_label VARCHAR(150) DEFAULT NULL,
    opening_hours VARCHAR(120) DEFAULT NULL,
    about_text TEXT,
    significance_text TEXT,
    image_theme VARCHAR(80) NOT NULL DEFAULT 'theme-temple-stone'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pujas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    temple_id INT UNSIGNED NOT NULL,
    name VARCHAR(180) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    deity VARCHAR(120) NOT NULL,
    duration_label VARCHAR(80) DEFAULT NULL,
    devotees_count INT NOT NULL DEFAULT 0,
    description TEXT,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    original_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    discount_label VARCHAR(40) DEFAULT NULL,
    image_theme VARCHAR(80) NOT NULL DEFAULT 'theme-marigold',
    benefits_json JSON DEFAULT NULL,
    CONSTRAINT fk_pujas_temple FOREIGN KEY (temple_id) REFERENCES temples(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    name VARCHAR(180) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    rating DECIMAL(3,1) NOT NULL DEFAULT 4.5,
    reviews_count INT NOT NULL DEFAULT 0,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    original_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    image_theme VARCHAR(80) NOT NULL DEFAULT 'theme-marigold',
    short_description VARCHAR(255) DEFAULT NULL,
    stock_qty INT NOT NULL DEFAULT 100,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sevas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    place_group VARCHAR(120) NOT NULL,
    description TEXT,
    live_stream_note VARCHAR(255) DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    original_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    badge_label VARCHAR(40) DEFAULT NULL,
    image_theme VARCHAR(80) NOT NULL DEFAULT 'theme-marigold'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS seva_events (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seva_id INT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    event_date DATE NOT NULL,
    temple_location VARCHAR(180) DEFAULT NULL,
    place_group VARCHAR(120) NOT NULL,
    event_type VARCHAR(120) DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    description TEXT,
    CONSTRAINT fk_seva_events_seva FOREIGN KEY (seva_id) REFERENCES sevas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS puja_bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    puja_id INT UNSIGNED NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    phone VARCHAR(32) NOT NULL,
    gotra VARCHAR(120) DEFAULT NULL,
    preferred_date DATE DEFAULT NULL,
    occasion VARCHAR(180) DEFAULT NULL,
    address_line VARCHAR(255) NOT NULL,
    city VARCHAR(120) NOT NULL,
    state VARCHAR(120) NOT NULL,
    pincode VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    status VARCHAR(40) NOT NULL DEFAULT 'received',
    payment_status VARCHAR(40) NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_puja_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_puja_bookings_puja FOREIGN KEY (puja_id) REFERENCES pujas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS seva_bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    seva_id INT UNSIGNED NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    phone VARCHAR(32) NOT NULL,
    booking_for VARCHAR(160) DEFAULT NULL,
    preferred_date DATE DEFAULT NULL,
    occasion VARCHAR(180) DEFAULT NULL,
    address_line VARCHAR(255) NOT NULL,
    city VARCHAR(120) NOT NULL,
    state VARCHAR(120) NOT NULL,
    pincode VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    status VARCHAR(40) NOT NULL DEFAULT 'received',
    payment_status VARCHAR(40) NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_seva_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_seva_bookings_seva FOREIGN KEY (seva_id) REFERENCES sevas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    phone VARCHAR(32) NOT NULL,
    address_line VARCHAR(255) NOT NULL,
    city VARCHAR(120) NOT NULL,
    state VARCHAR(120) NOT NULL,
    pincode VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    status VARCHAR(40) NOT NULL DEFAULT 'received',
    payment_status VARCHAR(40) NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED DEFAULT NULL,
    product_name VARCHAR(180) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    line_total DECIMAL(10,2) NOT NULL DEFAULT 0,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (full_name, email, password_hash, phone, age, gender, gotra, address_line, city, state, pincode, role, created_at) VALUES
('Admin', 'admin@devlokam.com', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', '+918800077181', 25, 'Male', 'Gaud', '78, Chanakya Puri', 'Agra', 'Uttar Pradesh', '282010', 'admin', NOW()),
('Bhakti Sharma', 'bhakti@devlokam.com', '633d11a05151d7fdcf60155688c2198c6214382598f79b5e036d35a77e044f68', '+919999111222', 32, 'Female', 'Kashyap', '14 Mandir Marg', 'Delhi', 'Delhi', '110001', 'devotee', NOW());

INSERT INTO temples (name, slug, deity_primary, deity_secondary, city, state, region_label, opening_hours, about_text, significance_text, image_theme) VALUES
('Kashi Vishwanath Temple', 'kashi-vishwanath-temple', 'Lord Shiva', 'Goddess Parvati', 'Varanasi', 'Uttar Pradesh', 'Varanasi, Uttar Pradesh', '3:00 AM - 11:00 PM', 'One of the most famous Hindu temples dedicated to Lord Shiva, located on the western bank of the holy river Ganga.', 'One of the twelve Jyotirlingas and revered for liberation from the cycle of birth and death.', 'theme-temple-stone'),
('Tirupati Balaji Temple', 'tirupati-balaji-temple', 'Lord Venkateswara', 'Goddess Padmavati', 'Tirumala', 'Andhra Pradesh', 'Tirumala, Andhra Pradesh', '3:00 AM - 11:00 PM', 'A celebrated Vaishnavite temple known for centuries of devotion and pilgrimage.', 'Considered one of the richest and most visited temples in the world.', 'theme-temple-gold'),
('Somnath Temple', 'somnath-temple', 'Lord Shiva', 'Goddess Parvati', 'Gir Somnath', 'Gujarat', 'Gir Somnath, Gujarat', '6:00 AM - 9:30 PM', 'Somnath stands on the western coast as one of the most sacred Shiva shrines.', 'The first of the twelve Jyotirlinga temples and a symbol of spiritual resilience.', 'theme-diya'),
('Mahakaleshwar Temple', 'mahakaleshwar-temple', 'Lord Shiva', 'Goddess Parvati', 'Ujjain', 'Madhya Pradesh', 'Ujjain, Madhya Pradesh', '4:00 AM - 11:00 PM', 'Mahakaleshwar is revered for powerful Shiva worship and ancient ritual tradition.', 'Famous for Bhasma Aarti and deep Shaiva heritage.', 'theme-plate'),
('Jagannath Temple', 'jagannath-temple', 'Lord Jagannath', 'Subhadra', 'Puri', 'Odisha', 'Puri, Odisha', '5:00 AM - 10:00 PM', 'The temple of Lord Jagannath is renowned for the annual Rath Yatra and Mahaprasad tradition.', 'A major Vaishnavite pilgrimage center in eastern India.', 'theme-temple-stone'),
('Meenakshi Amman Temple', 'meenakshi-amman-temple', 'Goddess Meenakshi', 'Lord Sundareswarar', 'Madurai', 'Tamil Nadu', 'Madurai, Tamil Nadu', '5:00 AM - 9:30 PM', 'A grand Dravidian temple complex known for intricate gopurams and active worship.', 'Celebrated for sacred marriage rituals and rich cultural heritage.', 'theme-temple-gold');

INSERT INTO pujas (temple_id, name, slug, deity, duration_label, devotees_count, description, price, original_price, is_featured, discount_label, image_theme, benefits_json) VALUES
((SELECT id FROM temples WHERE slug = 'kashi-vishwanath-temple'), 'Rudrabhishek Puja', 'rudrabhishek-puja', 'Lord Shiva', '2-3 Hours', 12450, 'Sacred Rudrabhishek performed with Panchamrit, Bilva leaves and Vedic mantras for Lord Shiva''s divine blessings.', 2100, 3100, 1, '32% OFF', 'theme-diya', JSON_ARRAY('Remove negativity', 'Spiritual growth', 'Health and prosperity', 'Peace of mind')),
((SELECT id FROM temples WHERE slug = 'tirupati-balaji-temple'), 'Satyanarayan Puja', 'satyanarayan-puja', 'Lord Vishnu', '1.5-2 Hours', 9000, 'Invoke Lord Vishnu''s grace through this traditional puja for fulfillment of desires and family harmony.', 1500, 2100, 1, '29% OFF', 'theme-marigold', JSON_ARRAY('Family well-being', 'Prosperity', 'Blessings for new beginnings', 'Inner stability')),
((SELECT id FROM temples WHERE slug = 'mahakaleshwar-temple'), 'Maha Mrityunjaya Jaap', 'maha-mrityunjaya-jaap', 'Lord Shiva', '4-5 Hours', 6800, 'Powerful chanting of Maha Mrityunjaya Mantra for protection from obstacles and improved vitality.', 3500, 5100, 1, '31% OFF', 'theme-plate', JSON_ARRAY('Protection from fear', 'Healing energy', 'Long life blessings', 'Mental peace')),
((SELECT id FROM temples WHERE slug = 'somnath-temple'), 'Navagraha Shanti Puja', 'navagraha-shanti-puja', 'Navagraha', '3-4 Hours', 5400, 'Appease planetary doshas and invite cosmic harmony through a guided Navagraha ritual.', 2500, 3500, 1, '29% OFF', 'theme-diya', JSON_ARRAY('Planetary balance', 'Career support', 'Marital harmony', 'Financial alignment')),
((SELECT id FROM temples WHERE slug = 'jagannath-temple'), 'Griha Shanti Puja', 'griha-shanti-puja', 'Lord Jagannath', '2 Hours', 4300, 'Bless your home with a peaceful and auspicious ritual before moving in or starting a new chapter.', 1800, 2400, 0, '25% OFF', 'theme-marigold', JSON_ARRAY('Household peace', 'Good fortune', 'Protection from disturbances', 'Positive vibrations')),
((SELECT id FROM temples WHERE slug = 'meenakshi-amman-temple'), 'Lakshmi Kubera Homam', 'lakshmi-kubera-homam', 'Goddess Lakshmi', '3 Hours', 5100, 'Traditional homam invoking Lakshmi and Kubera for abundance, stability and respectful growth.', 3200, 4200, 0, '24% OFF', 'theme-sandalwood', JSON_ARRAY('Abundance', 'Business blessings', 'Debt relief prayers', 'Household stability'));

INSERT INTO product_categories (name, slug) VALUES
('Puja Items', 'puja-items'),
('Spiritual', 'spiritual'),
('Idols', 'idols'),
('Sacred', 'sacred'),
('Books', 'books');

INSERT INTO products (category_id, name, slug, rating, reviews_count, price, original_price, image_theme, short_description, stock_qty) VALUES
((SELECT id FROM product_categories WHERE slug = 'puja-items'), 'Brass Diya Set (Pack of 12)', 'brass-diya-set-pack-of-12', 4.7, 2340, 599, 899, 'theme-marigold', 'Festival-ready diya set for daily worship and gifting.', 50),
((SELECT id FROM product_categories WHERE slug = 'spiritual'), '5 Mukhi Rudraksha Mala', '5-mukhi-rudraksha-mala', 4.8, 1890, 1299, 2499, 'theme-plate', 'Traditional mala suited for chanting and meditation.', 75),
((SELECT id FROM product_categories WHERE slug = 'sacred'), 'Pure Sandalwood Paste (50g)', 'pure-sandalwood-paste-50g', 4.6, 3210, 349, 499, 'theme-diya', 'Temple-style sandal paste for tilak and worship.', 200),
((SELECT id FROM product_categories WHERE slug = 'puja-items'), 'Premium Agarbatti Set', 'premium-agarbatti-set', 4.5, 5670, 449, 650, 'theme-marigold', 'Natural fragrance sticks for puja and meditation spaces.', 120),
((SELECT id FROM product_categories WHERE slug = 'books'), 'Bhagavad Gita Pocket Edition', 'bhagavad-gita-pocket-edition', 4.9, 980, 299, 399, 'theme-sandalwood', 'Compact scripture edition for daily reading.', 90),
((SELECT id FROM product_categories WHERE slug = 'idols'), 'Marble Ganesha Idol', 'marble-ganesha-idol', 4.7, 740, 1499, 1899, 'theme-temple-stone', 'A handcrafted idol for home mandirs.', 35);

INSERT INTO sevas (name, slug, place_group, description, live_stream_note, price, original_price, badge_label, image_theme) VALUES
('Prasad Delivery', 'prasad-delivery', 'Multiple Temples', 'Receive blessed prasad from famous temples delivered to your doorstep.', 'Live seva link shared after booking', 299, 499, '40% OFF', 'theme-marigold'),
('Gau Mata Seva', 'gau-mata-seva', 'Goverdhan', 'Sponsor feeding and care of sacred cows at Goverdhan gaushala.', 'Live seva link shared after booking', 501, 701, '28% OFF', 'theme-plate'),
('Brahman Bhojan', 'brahman-bhojan', 'Kashi / Prayagraj', 'Sponsor Brahman Bhojan at sacred pilgrim sites for merit and blessings.', 'Live seva link shared after booking', 1100, 1500, '31% OFF', 'theme-diya'),
('Bhandara', 'bhandara', 'Vrindavan', 'Organize community bhandara at temples and support mass feeding.', 'Live seva link shared after booking', 5100, 7100, '27% OFF', 'theme-marigold'),
('Giriraj Parikrama Seva', 'giriraj-parikrama-seva', 'Goverdhan', 'Support devotees during parikrama season with seva and offerings.', 'Live seva link shared after booking', 1100, 1500, '29% OFF', 'theme-sandalwood'),
('Chappan Bhog Seva', 'chappan-bhog-seva', 'Nathdwara', 'Offer a festive bhog seva during auspicious temple days.', 'Live seva link shared after booking', 2100, 3100, '32% OFF', 'theme-temple-gold');

INSERT INTO seva_events (seva_id, title, event_date, temple_location, place_group, event_type, price, description) VALUES
((SELECT id FROM sevas WHERE slug = 'bhandara'), 'Akshaya Tritiya Bhandara', '2026-04-29', 'Vrindavan', 'Vrindavan', 'Bhandara', 5100, 'Grand bhandara on the auspicious day of Akshaya Tritiya at Vrindavan temples.'),
((SELECT id FROM sevas WHERE slug = 'giriraj-parikrama-seva'), 'Goverdhan Parikrama Seva', '2026-05-01', 'Goverdhan', 'Goverdhan', 'Gau Seva + Giriraj Puja', 1100, 'Special Gau Seva and Giriraj Puja during Goverdhan Parikrama season.'),
((SELECT id FROM sevas WHERE slug = 'chappan-bhog-seva'), 'Chappan Bhog - Shrinathji Utsav', '2026-05-12', 'Nathdwara', 'Nathdwara', 'Chappan Bhog', 2100, 'Special Chappan Bhog offering during Shrinathji temple festival.'),
((SELECT id FROM sevas WHERE slug = 'brahman-bhojan'), 'Ganga Snan & Brahman Bhojan', '2026-05-15', 'Prayagraj', 'Kashi / Prayagraj', 'Brahman Bhojan', 1100, 'Sponsor Brahman Bhojan on the sacred banks of Ganga during Jyeshtha month.'),
((SELECT id FROM sevas WHERE slug = 'prasad-delivery'), 'Festival Prasad Dispatch', '2026-06-05', 'Multiple Temples', 'Multiple Temples', 'Prasad Delivery', 299, 'Temple prasad shipped after the monthly festival cycle.');
