import { VoteService } from '../../assets/js/services/VoteService.js';

describe('VoteService', () => {
    beforeEach(() => {
        fetch.mockClear();
    });

    test('should submit votes successfully', async () => {
        const mockResponse = {
            success: true,
            message: 'Vote submitted successfully'
        };
        
        fetch.mockResolvedValueOnce({
            ok: true,
            json: () => Promise.resolve(mockResponse)
        });

        const votes = [
            { position: 'position1', candidateId: 'candidate1' }
        ];

        const result = await VoteService.submitVotes(votes);

        expect(fetch).toHaveBeenCalledWith('vote.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ votes })
        });

        expect(result).toEqual({
            success: true,
            message: 'Vote submitted successfully'
        });
    });

    test('should handle submission error', async () => {
        fetch.mockRejectedValueOnce(new Error('Network error'));

        const votes = [
            { position: 'position1', candidateId: 'candidate1' }
        ];

        const result = await VoteService.submitVotes(votes);

        expect(result).toEqual({
            success: false,
            message: 'Network error'
        });
    });

    test('should check voting status successfully', async () => {
        const mockResponse = {
            isOpen: true,
            message: 'Voting is open'
        };
        
        fetch.mockResolvedValueOnce({
            ok: true,
            json: () => Promise.resolve(mockResponse)
        });

        const result = await VoteService.getVotingStatus();

        expect(fetch).toHaveBeenCalledWith('check_voting_status.php');
        expect(result).toEqual({
            success: true,
            isOpen: true,
            message: 'Voting is open'
        });
    });

    test('should handle voting status check error', async () => {
        fetch.mockRejectedValueOnce(new Error('Network error'));

        const result = await VoteService.getVotingStatus();

        expect(result).toEqual({
            success: false,
            message: 'Network error'
        });
    });

    test('should test connection successfully', async () => {
        const mockResponse = { status: 'ok' };
        
        fetch.mockResolvedValueOnce({
            ok: true,
            json: () => Promise.resolve(mockResponse)
        });

        const result = await VoteService.testConnection();

        expect(fetch).toHaveBeenCalledWith('api/vote.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ test: true })
        });

        expect(result).toEqual(mockResponse);
    });
}); 