CREATE TABLE what_we_do (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(50) NOT NULL,
    features TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO what_we_do (title, description, icon, features) VALUES
('Plastic Waste Management', 'We provide comprehensive waste collection and sorting services for communities, institutions, and businesses across Zambia.', '🗑️', 'Door-to-door waste collection|Waste segregation at source|Community waste collection points|Institutional waste management programs'),
('Plastic Recycling', 'Using advanced recycling technology, we transform plastic waste into valuable raw materials.', '♻️', 'Sorting and cleaning of plastic waste|Shredding and processing|Quality control and testing|Raw material production'),
('Eco-Friendly Paver & Tile Production', 'We manufacture high-quality, durable pavers and tiles from 100% recycled plastic.', '🧱', 'Durable and long-lasting|Water and UV resistant|Low maintenance requirements|Customizable designs and colors');