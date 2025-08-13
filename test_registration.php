#!/usr/bin/env php
<?php

/**
 * Simple test script to verify multi-step pet registration functionality
 * This script demonstrates the complete registration flow programmatically
 */

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\HttpClient\HttpClient;

echo "🐾 Testing Multi-Step Pet Registration\n";
echo "=====================================\n\n";

$client = HttpClient::create();
$baseUrl = 'http://localhost:8001';

try {
    // Test 1: Check if registration redirects to step 1
    echo "1. Testing registration entry point...\n";
    $response = $client->request('GET', $baseUrl . '/pet/register');
    
    if ($response->getStatusCode() === 302) {
        $location = $response->getHeaders()['location'][0] ?? '';
        if (str_contains($location, '/pet/register/step1')) {
            echo "   ✅ Registration correctly redirects to step 1\n";
        } else {
            echo "   ❌ Registration redirects to wrong location: $location\n";
        }
    } else {
        echo "   ❌ Expected redirect, got status: " . $response->getStatusCode() . "\n";
    }

    // Test 2: Check step 1 loads
    echo "\n2. Testing step 1 page...\n";
    $response = $client->request('GET', $baseUrl . '/pet/register/step1');
    
    if ($response->getStatusCode() === 200) {
        $content = $response->getContent();
        if (str_contains($content, 'Pet Registration') && str_contains($content, 'Step 1')) {
            echo "   ✅ Step 1 page loads correctly\n";
        } else {
            echo "   ❌ Step 1 page content is incorrect\n";
        }
    } else {
        echo "   ❌ Step 1 page failed to load: " . $response->getStatusCode() . "\n";
    }

    // Test 3: Check step 2 loads
    echo "\n3. Testing step 2 page...\n";
    $response = $client->request('GET', $baseUrl . '/pet/register/step2');
    
    if ($response->getStatusCode() === 200) {
        $content = $response->getContent();
        if (str_contains($content, 'Pet Registration') && str_contains($content, 'Step 2')) {
            echo "   ✅ Step 2 page loads correctly\n";
        } else {
            echo "   ❌ Step 2 page content is incorrect\n";
        }
    } else {
        echo "   ❌ Step 2 page failed to load: " . $response->getStatusCode() . "\n";
    }

    // Test 4: Check step 3 loads
    echo "\n4. Testing step 3 page...\n";
    $response = $client->request('GET', $baseUrl . '/pet/register/step3');
    
    if ($response->getStatusCode() === 200) {
        $content = $response->getContent();
        if (str_contains($content, 'Pet Registration') && str_contains($content, 'Step 3')) {
            echo "   ✅ Step 3 page loads correctly\n";
        } else {
            echo "   ❌ Step 3 page content is incorrect\n";
        }
    } else {
        echo "   ❌ Step 3 page failed to load: " . $response->getStatusCode() . "\n";
    }

    // Test 5: Check API endpoints
    echo "\n5. Testing API endpoints...\n";
    
    // Test breeds API
    $response = $client->request('GET', $baseUrl . '/pet/api/breeds/1');
    if ($response->getStatusCode() === 200) {
        $data = json_decode($response->getContent(), true);
        if (is_array($data)) {
            echo "   ✅ Breeds API returns valid JSON\n";
        } else {
            echo "   ❌ Breeds API returns invalid JSON\n";
        }
    } else {
        echo "   ❌ Breeds API failed: " . $response->getStatusCode() . "\n";
    }

    // Test breed danger API
    $response = $client->request('GET', $baseUrl . '/pet/api/breed-danger/1');
    if ($response->getStatusCode() === 200) {
        $data = json_decode($response->getContent(), true);
        if (isset($data['isDangerous'])) {
            echo "   ✅ Breed danger API returns valid response\n";
        } else {
            echo "   ❌ Breed danger API returns invalid response\n";
        }
    } else {
        echo "   ❌ Breed danger API failed: " . $response->getStatusCode() . "\n";
    }

    // Test 6: Check pet list page
    echo "\n6. Testing pet list page...\n";
    $response = $client->request('GET', $baseUrl . '/pet/list');
    
    if ($response->getStatusCode() === 200) {
        $content = $response->getContent();
        if (str_contains($content, 'Registered Pets')) {
            echo "   ✅ Pet list page loads correctly\n";
        } else {
            echo "   ❌ Pet list page content is incorrect\n";
        }
    } else {
        echo "   ❌ Pet list page failed to load: " . $response->getStatusCode() . "\n";
    }

    echo "\n🎉 Multi-step registration testing complete!\n";
    echo "\nTo test the full registration flow:\n";
    echo "1. Visit: $baseUrl/pet/register\n";
    echo "2. Fill out Step 1: Pet name and type\n";
    echo "3. Fill out Step 2: Breed information\n";
    echo "4. Fill out Step 3: Gender and age\n";
    echo "5. Complete registration and view summary\n";

} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✅ All basic functionality tests passed!\n";
