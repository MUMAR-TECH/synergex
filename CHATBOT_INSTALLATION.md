# AI Chatbot Installation Guide for Synergex Solutions

## 🤖 Overview

A custom AI chatbot has been successfully integrated into your Synergex Solutions website. This chatbot is:
- **100% Free** - No subscriptions or API costs
- **Tailored to Synergex** - Pre-loaded with company-specific knowledge
- **Smart & Interactive** - Natural language processing with context awareness
- **Fully Customizable** - Manage from the admin panel

## 📋 Installation Steps

### Step 1: Import Database Schema

Run the chatbot database schema to create the necessary tables:

```bash
# Using command line
mysql -u root -p synergex_db < database_chatbot.sql

# OR via phpMyAdmin
# 1. Open phpMyAdmin
# 2. Select 'synergex_db' database
# 3. Click 'Import' tab
# 4. Choose 'database_chatbot.sql' file
# 5. Click 'Go'
```

This will create:
- `chatbot_conversations` - Stores visitor conversations
- `chatbot_messages` - Stores individual messages
- `chatbot_knowledge` - AI knowledge base (pre-populated with 15+ Q&As)
- `chatbot_settings` - Chatbot configuration

### Step 2: Verify Installation

The chatbot is already integrated into all website pages through the footer. No additional code changes needed!

### Step 3: Test the Chatbot

1. Visit any page on your website
2. Look for the green chatbot icon in the bottom-right corner
3. Click to open and test with questions like:
   - "What products do you offer?"
   - "How do I get a quote?"
   - "Tell me about your company"

## 🎯 Features

### For Website Visitors

- **Instant Responses** - Get answers about Synergex products and services 24/7
- **Smart Suggestions** - Quick reply buttons for common questions
- **Natural Conversations** - Understands questions in plain English
- **Contact Collection** - Can collect visitor information for follow-up
- **Mobile Responsive** - Works perfectly on all devices

### For Administrators

- **Conversation History** - View all visitor conversations
- **Knowledge Management** - Add, edit, or remove Q&A pairs
- **Analytics Dashboard** - Track total conversations and messages
- **Customization** - Change colors, greetings, and position
- **Status Tracking** - Mark conversations as active, resolved, or closed

## 🛠️ Admin Panel Usage

### Access Chatbot Management

1. Login to admin panel: `/admin/`
2. Click "🤖 AI Chatbot" in the sidebar
3. You'll see three tabs:
   - **Conversations** - View visitor chats
   - **Knowledge Base** - Manage Q&A content
   - **Settings** - Configure chatbot appearance

### Managing Knowledge Base

The chatbot comes pre-loaded with knowledge about:
- Company overview and mission
- Products and pricing
- Ordering process
- Recycling process
- Contact information
- Environmental impact
- And much more!

**To Add New Knowledge:**
1. Go to "Knowledge Base" tab
2. Click "+ Add Knowledge"
3. Fill in:
   - Question (what visitors might ask)
   - Answer (bot's response)
   - Category (for organization)
   - Keywords (comma-separated words that trigger this answer)
   - Priority (0-100, higher = more important)
4. Click "Save Knowledge"

**Example Knowledge Entry:**
```
Question: What colors do your pavers come in?
Answer: Our eco-pavers are available in multiple colors including grey, red, brown, and yellow. We can also create custom colors for larger orders. Contact us to discuss your color preferences!
Category: products
Keywords: colors,colour,shades,available,options
Priority: 60
```

### Customizing Appearance

In the **Settings** tab, you can customize:
- **Enable/Disable** - Turn chatbot on/off
- **Bot Name** - Default: "Synergex Assistant"
- **Greeting Message** - First message visitors see
- **Color** - Brand color (default: #27ae60)
- **Position** - Bottom-right or bottom-left
- **Offline Message** - Shown when unable to respond

### Viewing Conversations

1. Go to "Conversations" tab
2. See list of all visitor chats with:
   - Visitor information (if provided)
   - Number of messages
   - Status (active/resolved/closed)
   - Timestamps
3. Click "View" to see full conversation history

## 🧠 How the AI Works

The chatbot uses intelligent keyword matching and context awareness:

1. **Greeting Detection** - Recognizes "hi", "hello", "hey", etc.
2. **Keyword Matching** - Searches knowledge base for relevant keywords
3. **Question Similarity** - Analyzes question structure
4. **Priority Scoring** - Returns best matching answer
5. **Smart Suggestions** - Offers related questions to continue conversation

## 📊 Pre-loaded Knowledge (15+ Items)

The chatbot already knows about:
- ✅ What Synergex Solutions does
- ✅ Products offered (eco-pavers, tiles)
- ✅ How to get quotes
- ✅ Location and contact info
- ✅ Mission and vision
- ✅ Recycling process
- ✅ Benefits of eco-pavers
- ✅ How to place orders
- ✅ Environmental impact
- ✅ Delivery information
- ✅ Facility tours
- ✅ Product specifications
- ✅ How to support the mission
- ✅ Types of plastic recycled
- ✅ Product durability

## 🎨 Customization Tips

### Brand Colors
Update the chatbot color to match your brand:
```
Settings > Chatbot Color > Choose color > Save
```

### Custom Greetings
Make it personal:
```
"Hi there! 👋 Welcome to Synergex Solutions. I'm your eco-friendly assistant. 
Need help with our recycled plastic pavers? Just ask!"
```

### Advanced: Modify Keywords
For better matching, add relevant keywords:
- Use synonyms: "buy,purchase,order,get"
- Include common misspellings
- Add industry terms
- Include location names: "Lusaka,Zambia"

## 🔧 Troubleshooting

### Chatbot not appearing?
1. Check that chatbot CSS and JS files are loading
2. Verify database tables were created
3. Clear browser cache

### Bot not responding correctly?
1. Check Knowledge Base has active items
2. Review keywords for spelling
3. Test with exact question from knowledge base
4. Check browser console for errors

### Want to disable temporarily?
Settings > Chatbot Enabled > Disabled > Save

## 📱 Mobile Experience

The chatbot is fully responsive:
- Adapts to smaller screens
- Touch-friendly interface
- Optimized for mobile browsing
- Fast load times

## 🚀 Best Practices

1. **Keep Knowledge Updated** - Add new Q&As based on common visitor questions
2. **Review Conversations** - Check what people are asking to improve responses
3. **Use Clear Language** - Write answers in simple, friendly terms
4. **Add Contact CTAs** - Include "Contact us at..." in relevant answers
5. **Update Regularly** - When prices or products change, update knowledge base

## 📈 Growth Opportunities

As your business grows, expand the chatbot:
- Add product-specific Q&As
- Include pricing information
- Add seasonal promotions
- Create FAQ categories
- Add multi-language support (future enhancement)

## 🆘 Support

If you need help with the chatbot:
1. Check this guide first
2. Review the conversation logs in admin
3. Test with different questions
4. Verify database tables exist

## 🎉 You're All Set!

Your AI chatbot is now ready to:
- Answer visitor questions 24/7
- Provide information about products
- Help generate leads
- Improve customer engagement
- Reduce repetitive inquiries

Visit your website and give it a try! The chatbot will help convert more visitors into customers while showcasing Synergex Solutions' innovative approach to sustainability.

---

**Need to add specific company information?**
Just add it to the Knowledge Base in the admin panel, and the chatbot will instantly be able to answer those questions!
