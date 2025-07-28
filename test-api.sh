#!/bin/bash

API_URL="http://localhost:8080/api"

echo "🍎🥕 Testing Fruits and Vegetables API"
echo "====================================="

# Load data
echo -e "\n1. Loading initial data..."
curl -X POST ${API_URL}/load-data

# Get all collections
echo -e "\n\n2. Getting all collections..."
curl ${API_URL}/collections | jq '.'

# Get fruits only
echo -e "\n\n3. Getting fruits collection..."
curl ${API_URL}/collections/fruit | jq '.'

# Get vegetables with search
echo -e "\n\n4. Searching for 'carrot' in vegetables..."
curl "${API_URL}/collections/vegetable?search=carrot" | jq '.'

# Get fruits in kilograms
echo -e "\n\n5. Getting fruits in kilograms..."
curl "${API_URL}/collections/fruit?unit=kg" | jq '.'

# Add a new fruit
echo -e "\n\n6. Adding a new fruit (Strawberries)..."
curl -X POST ${API_URL}/items \
  -H "Content-Type: application/json" \
  -d '{
    "id": 100,
    "name": "Strawberries",
    "type": "fruit",
    "quantity": 2.5,
    "unit": "kg"
  }' | jq '.'

# Get fruits again to see the new item
echo -e "\n\n7. Getting fruits collection again..."
curl ${API_URL}/collections/fruit | jq '.'

# Delete the item
echo -e "\n\n8. Removing Strawberries..."
curl -X DELETE ${API_URL}/items/fruit/100 | jq '.'

echo -e "\n\n✅ API testing complete!"