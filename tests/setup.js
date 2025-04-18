// Test setup configuration
import { JSDOM } from 'jsdom';

// Create a DOM environment
const dom = new JSDOM('<!DOCTYPE html><html><body></body></html>');
global.document = dom.window.document;
global.window = dom.window;

// Mock localStorage
const localStorageMock = (() => {
    let store = {};
    return {
        getItem: key => store[key] || null,
        setItem: (key, value) => {
            store[key] = value.toString();
        },
        removeItem: key => {
            delete store[key];
        },
        clear: () => {
            store = {};
        }
    };
})();

global.localStorage = localStorageMock;

// Mock fetch
global.fetch = jest.fn();

// Helper function to create test form
export const createTestForm = () => {
    const form = document.createElement('form');
    form.id = 'votingForm';
    
    // Create position group
    const positionGroup = document.createElement('div');
    positionGroup.className = 'position-group';
    
    // Create radio inputs
    for (let i = 1; i <= 3; i++) {
        const input = document.createElement('input');
        input.type = 'radio';
        input.name = `votes[position${i}]`;
        input.value = `candidate${i}`;
        positionGroup.appendChild(input);
    }
    
    form.appendChild(positionGroup);
    document.body.appendChild(form);
    return form;
};

// Helper function to create test candidate card
export const createTestCandidateCard = () => {
    const card = document.createElement('div');
    card.className = 'candidate-card';
    
    const radio = document.createElement('input');
    radio.type = 'radio';
    radio.name = 'votes[position1]';
    radio.value = 'candidate1';
    
    const bio = document.createElement('div');
    bio.className = 'candidate-bio';
    bio.textContent = 'Test bio';
    
    const showMoreBtn = document.createElement('button');
    showMoreBtn.className = 'show-more-btn';
    showMoreBtn.textContent = 'Show More';
    
    card.appendChild(radio);
    card.appendChild(bio);
    card.appendChild(showMoreBtn);
    
    return card;
}; 