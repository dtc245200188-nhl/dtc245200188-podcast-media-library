-- =============================================
-- Podcast Media Library - Database Schema & Seed
-- =============================================

-- Use the database created by MYSQL_DATABASE env var
-- The database is already created by the MySQL container

-- Exporter user for monitoring
CREATE USER IF NOT EXISTS 'exporter_user'@'%' IDENTIFIED BY 'matkhaumanh';
GRANT PROCESS, REPLICATION CLIENT, SELECT ON *.* TO 'exporter_user'@'%';
FLUSH PRIVILEGES;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Episodes table
CREATE TABLE IF NOT EXISTS episodes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    audio_file VARCHAR(500) DEFAULT NULL,
    cover_image VARCHAR(500) DEFAULT NULL,
    category_id INT DEFAULT NULL,
    duration VARCHAR(20) DEFAULT '00:00',
    published_at DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category (category_id),
    INDEX idx_published (published_at),
    FULLTEXT INDEX idx_search (title, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Seed Data
-- =============================================

-- Seed categories
INSERT INTO categories (name, slug, description) VALUES
('Công nghệ', 'cong-nghe', 'Podcast về công nghệ, lập trình, AI và xu hướng digital mới nhất. Cập nhật kiến thức tech mỗi tuần.'),
('Kinh doanh', 'kinh-doanh', 'Podcast về khởi nghiệp, marketing số, quản trị doanh nghiệp và chiến lược phát triển bền vững.'),
('Khoa học', 'khoa-hoc', 'Podcast khám phá thế giới khoa học, từ vũ trụ bao la đến thế giới vi mô, giải đáp những bí ẩn tự nhiên.'),
('Lối sống', 'loi-song', 'Podcast về phát triển bản thân, sức khỏe tinh thần, và xây dựng lối sống cân bằng, hạnh phúc.'),
('Giáo dục', 'giao-duc', 'Podcast về giáo dục, phương pháp học tập hiệu quả và định hướng nghề nghiệp cho thế hệ trẻ.');

-- Seed episodes
INSERT INTO episodes (title, slug, description, category_id, duration, published_at) VALUES
('Tương lai của AI trong 2025', 'tuong-lai-cua-ai-2025',
 'Khám phá những xu hướng AI mới nhất và tác động của chúng đến cuộc sống hàng ngày. Từ ChatGPT đến các mô hình ngôn ngữ lớn, chúng ta sẽ thảo luận về tương lai của trí tuệ nhân tạo và cách nó đang thay đổi mọi ngành nghề.',
 1, '45:30', '2025-01-15'),

('Docker và Kubernetes cho người mới bắt đầu', 'docker-kubernetes-nguoi-moi',
 'Hướng dẫn cơ bản về containerization với Docker và orchestration với Kubernetes. Từ việc viết Dockerfile đầu tiên đến deploy ứng dụng trên K8s cluster, bắt đầu hành trình DevOps của bạn.',
 1, '38:15', '2025-02-20'),

('Khởi nghiệp từ số 0 - Bài học thực tế', 'khoi-nghiep-tu-so-0',
 'Chia sẻ kinh nghiệm xây dựng startup từ con số 0 với những bài học xương máu. Founder của nhiều startup thành công tại Việt Nam chia sẻ về gọi vốn, xây dựng team, và vượt qua thất bại.',
 2, '52:00', '2025-03-10'),

('Marketing số thời đại mới', 'marketing-so-thoi-dai-moi',
 'Chiến lược marketing digital hiệu quả trong năm 2025. Từ social media marketing, content marketing, đến SEO và paid advertising - những trend mới nhất và cách áp dụng cho doanh nghiệp Việt.',
 2, '41:45', '2025-04-05'),

('Bí ẩn của vũ trụ - Hố đen và Vật chất tối', 'bi-an-cua-vu-tru',
 'Khám phá những bí ẩn chưa được giải đáp của vũ trụ bao la. Từ hố đen siêu khối lượng đến vật chất tối bí ẩn, hành trình tìm hiểu cosmos cùng các nhà khoa học hàng đầu Việt Nam.',
 3, '55:20', '2025-05-12'),

('Biến đổi khí hậu - Thực trạng và giải pháp', 'bien-doi-khi-hau',
 'Phân tích tình trạng biến đổi khí hậu hiện tại và các giải pháp tiềm năng. Vai trò của công nghệ xanh, năng lượng tái tạo trong cuộc chiến chống biến đổi khí hậu toàn cầu.',
 3, '48:10', '2025-06-18'),

('Mindfulness - Thiền định trong thời đại số', 'mindfulness-thien-dinh',
 'Hướng dẫn thực hành thiền định và mindfulness cho người bận rộn. Cách giảm stress, tăng tập trung và cải thiện sức khỏe tinh thần trong cuộc sống hiện đại đầy áp lực.',
 4, '35:45', '2025-07-22'),

('Học lập trình từ đâu trong 2025?', 'hoc-lap-trinh-2025',
 'Hướng dẫn lộ trình học lập trình hiệu quả cho người mới bắt đầu trong năm 2025. So sánh các ngôn ngữ, framework phổ biến và lời khuyên từ các developer senior.',
 5, '42:30', '2025-08-14'),

('Web3 và Blockchain - Hype hay Revolution?', 'web3-blockchain-hype-hay-revolution',
 'Phân tích khách quan về Web3, blockchain, NFT và DeFi. Đâu là tiềm năng thực sự và đâu chỉ là hype? Góc nhìn từ cả developer lẫn nhà đầu tư.',
 1, '50:15', '2025-09-01'),

('Quản lý tài chính cá nhân cho GenZ', 'quan-ly-tai-chinh-genz',
 'Hướng dẫn quản lý tài chính, tiết kiệm và đầu tư thông minh cho thế hệ GenZ. Từ budgeting cơ bản đến xây dựng portfolio đầu tư đầu tiên.',
 2, '44:00', '2025-09-15');

-- Set real cover images for episodes with uploaded photos
UPDATE episodes SET cover_image = '/uploads/covers/quan-ly-tai-chinh-genz.jpg' WHERE slug = 'quan-ly-tai-chinh-genz';
UPDATE episodes SET cover_image = '/uploads/covers/web3-blockchain-hype-hay-revolution.jpg' WHERE slug = 'web3-blockchain-hype-hay-revolution';
UPDATE episodes SET cover_image = '/uploads/covers/hoc-lap-trinh-2025.jpg' WHERE slug = 'hoc-lap-trinh-2025';
UPDATE episodes SET cover_image = '/uploads/covers/mindfulness-thien-dinh.jpg' WHERE slug = 'mindfulness-thien-dinh';
UPDATE episodes SET cover_image = '/uploads/covers/bien-doi-khi-hau.jpg' WHERE slug = 'bien-doi-khi-hau';
UPDATE episodes SET cover_image = '/uploads/covers/bi-an-cua-vu-tru.jpg' WHERE slug = 'bi-an-cua-vu-tru';
UPDATE episodes SET cover_image = '/uploads/covers/marketing-so-thoi-dai-moi.jpg' WHERE slug = 'marketing-so-thoi-dai-moi';
UPDATE episodes SET cover_image = '/uploads/covers/khoi-nghiep-tu-so-0.jpg' WHERE slug = 'khoi-nghiep-tu-so-0';
UPDATE episodes SET cover_image = '/uploads/covers/docker-kubernetes-nguoi-moi.jpg' WHERE slug = 'docker-kubernetes-nguoi-moi';
UPDATE episodes SET cover_image = '/uploads/covers/tuong-lai-cua-ai-2025.jpg' WHERE slug = 'tuong-lai-cua-ai-2025';
