<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuestionsSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $data = [

            // =========================
            // MODULE 1 — ID: 160
            // =========================
            [
                'module_id' => 160,
                'title' => 'What is the primary role of a Virtual Assistant (VA)?',
                'optionA' => 'Designing websites for businesses',
                'optionB' => 'Managing and supporting clients remotely with administrative or specialized tasks',
                'optionC' => 'Selling products on e-commerce platforms',
                'optionD' => 'Writing code for software applications',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 160,
                'title' => 'Which of the following is a soft skill every VA should develop?',
                'optionA' => 'Data Analysis',
                'optionB' => 'Graphic Design',
                'optionC' => 'Time Management',
                'optionD' => 'Coding',
                'correct' => 'C',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 160,
                'title' => 'Which tool is commonly used for managing tasks and projects as a VA?',
                'optionA' => 'Canva',
                'optionB' => 'Trello',
                'optionC' => 'Grammarly',
                'optionD' => 'Zoom',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 160,
                'title' => 'What is one benefit of working as a Virtual Assistant?',
                'optionA' => 'Fixed office hours only',
                'optionB' => 'No interaction with clients',
                'optionC' => 'Flexible work schedule',
                'optionD' => 'Limited to one type of task',
                'correct' => 'C',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 160,
                'title' => 'Which platform is widely used by VAs to find freelance job opportunities?',
                'optionA' => 'YouTube',
                'optionB' => 'Fiverr',
                'optionC' => 'Instagram',
                'optionD' => 'Spotify',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 160,
                'title' => 'What is a typical task a VA might be asked to do?',
                'optionA' => 'Cooking for the client',
                'optionB' => 'Managing emails and calendars',
                'optionC' => 'Attending physical meetings',
                'optionD' => 'Driving a company vehicle',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 160,
                'title' => 'Which of the following is a file-sharing tool useful for VAs?',
                'optionA' => 'Pinterest',
                'optionB' => 'WhatsApp',
                'optionC' => 'Google Drive',
                'optionD' => 'Photoshop',
                'correct' => 'C',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 160,
                'title' => 'What should a good VA profile include?',
                'optionA' => 'Hobbies and personal opinions',
                'optionB' => 'A compelling bio, skills, and relevant experience',
                'optionC' => 'Daily food diary',
                'optionD' => 'A list of favorite movies',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 160,
                'title' => 'Why is communication important for a VA?',
                'optionA' => 'To avoid speaking to clients',
                'optionB' => 'To confuse clients intentionally',
                'optionC' => 'To ensure tasks are understood and completed efficiently',
                'optionD' => 'It is not important',
                'correct' => 'C',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 160,
                'title' => 'Which tool would be best for tracking time spent on tasks?',
                'optionA' => 'Canva',
                'optionB' => 'Clockify',
                'optionC' => 'Facebook',
                'optionD' => 'Zoom',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],

            // =========================
            // MODULE 2 — ID: 161
            // =========================
            [
                'module_id' => 161,
                'title' => 'Which tool is primarily used for video meetings and virtual client calls?',
                'optionA' => 'Trello',
                'optionB' => 'Canva',
                'optionC' => 'Zoom',
                'optionD' => 'Slack',
                'correct' => 'C',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 161,
                'title' => 'What is the main function of Trello?',
                'optionA' => 'Creating social media designs',
                'optionB' => 'Managing and organizing tasks/projects using boards and cards',
                'optionC' => 'Editing videos',
                'optionD' => 'Sending bulk emails',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 161,
                'title' => 'Which tool helps VAs track how much time they spend on specific tasks?',
                'optionA' => 'Calendly',
                'optionB' => 'Canva',
                'optionC' => 'Toggl',
                'optionD' => 'Dropbox',
                'correct' => 'C',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 161,
                'title' => 'You need to share a large folder. Which tool should you use?',
                'optionA' => 'Google Calendar',
                'optionB' => 'Slack',
                'optionC' => 'WeTransfer',
                'optionD' => 'ChatGPT',
                'correct' => 'C',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 161,
                'title' => 'What is the main use of Calendly?',
                'optionA' => 'Editing PDF files',
                'optionB' => 'Scheduling meetings without email back-and-forth',
                'optionC' => 'Designing presentations',
                'optionD' => 'Tracking time spent on social media',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 161,
                'title' => 'Which tool allows you to create and share social media graphics?',
                'optionA' => 'Google Drive',
                'optionB' => 'Canva',
                'optionC' => 'Trello',
                'optionD' => 'Clockify',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 161,
                'title' => 'What is a key feature of Slack?',
                'optionA' => 'Creating spreadsheets',
                'optionB' => 'Video editing',
                'optionC' => 'Organizing team conversations into channels',
                'optionD' => 'Scheduling appointments',
                'correct' => 'C',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 161,
                'title' => 'Which CRM integrates directly with Gmail?',
                'optionA' => 'ClickUp',
                'optionB' => 'Asana',
                'optionC' => 'Streak',
                'optionD' => 'Google Calendar',
                'correct' => 'C',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 161,
                'title' => 'You want to manage social media posting for a client. Which tool?',
                'optionA' => 'Buffer',
                'optionB' => 'Streak',
                'optionC' => 'Dropbox',
                'optionD' => 'Clockify',
                'correct' => 'A',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 161,
                'title' => 'What is the purpose of creating a VA Tech Stack?',
                'optionA' => 'To stack books for posture',
                'optionB' => 'To build a portfolio',
                'optionC' => 'To identify tools used for different VA services',
                'optionD' => 'To learn how to code',
                'correct' => 'C',
                'created_at' => $now,
                'updated_at' => $now
            ],

            // =========================
            // MODULE 3 — ID: 162
            // =========================
            [
                'module_id' => 162,
                'title' => 'What is the purpose of creating filters in email management?',
                'optionA' => 'To delete unnecessary emails',
                'optionB' => 'To automatically sort and prioritize incoming emails',
                'optionC' => 'To forward emails to colleagues',
                'optionD' => 'To block spam emails',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 162,
                'title' => 'How would you prioritize emails in your inbox?',
                'optionA' => 'Respond in order received',
                'optionB' => 'Use the Eisenhower Matrix for urgency and importance',
                'optionC' => 'Ignore emails not addressed to you',
                'optionD' => 'Delete irrelevant emails',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 162,
                'title' => 'What is the advantage of using auto-replies?',
                'optionA' => 'To respond to urgent emails',
                'optionB' => 'To inform senders you are unavailable and will respond later',
                'optionC' => 'To automate promotions',
                'optionD' => 'To forward emails',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 162,
                'title' => 'How do you schedule a recurring meeting across different time zones?',
                'optionA' => 'Set a specific time zone and invite attendees',
                'optionB' => 'Use a time zone converter',
                'optionC' => 'Schedule in UTC',
                'optionD' => 'Create separate meetings for each zone',
                'correct' => 'A',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 162,
                'title' => 'What should be included in a standard business email signature?',
                'optionA' => 'Personal social media links',
                'optionB' => 'Company history',
                'optionC' => 'Name, title, company, and contact information',
                'optionD' => 'Confidentiality notice only',
                'correct' => 'C',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 162,
                'title' => 'What should be included in an invoice?',
                'optionA' => 'Company history',
                'optionB' => 'Employee job titles',
                'optionC' => 'Product descriptions and features',
                'optionD' => 'Invoice number, date, and payment terms',
                'correct' => 'D',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 162,
                'title' => 'How would you handle a difficult customer inquiry?',
                'optionA' => 'Respond aggressively',
                'optionB' => 'Acknowledge concern and provide solution',
                'optionC' => 'Ignore the inquiry',
                'optionD' => 'Escalate immediately',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 162,
                'title' => 'Best practice for managing virtual meetings?',
                'optionA' => 'Multitask during meeting',
                'optionB' => 'Test equipment and internet before meeting',
                'optionC' => 'Use speakerphone',
                'optionD' => 'Start late',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 162,
                'title' => 'How to ensure professionalism during video calls?',
                'optionA' => 'Wear casual clothes',
                'optionB' => 'Make eye contact, dress professionally, minimize distractions',
                'optionC' => 'Use virtual background',
                'optionD' => 'Check phone often',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 162,
                'title' => 'What is a key feature of customer service software?',
                'optionA' => 'Automated emails',
                'optionB' => 'Social media integration',
                'optionC' => 'Ticket assignment and escalation',
                'optionD' => 'All of the above',
                'correct' => 'D',
                'created_at' => $now,
                'updated_at' => $now
            ],

            // =========================
            // MODULE 4 — ID: 163
            // =========================
            [
                'module_id' => 163,
                'title' => 'Travel management mainly involves:',
                'optionA' => 'Booking only tickets',
                'optionB' => 'Planning, organizing, and coordinating client travel',
                'optionC' => 'Selling airline seats',
                'optionD' => 'Working at the airport',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 163,
                'title' => 'Which tool is BEST for comparing flight prices?',
                'optionA' => 'Canva',
                'optionB' => 'Uber',
                'optionC' => 'Skyscanner',
                'optionD' => 'Dropbox',
                'correct' => 'C',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 163,
                'title' => 'Airbnb is most suitable when:',
                'optionA' => 'Client needs kitchen space or long-term stay',
                'optionB' => 'It’s a one-day business trip',
                'optionC' => 'Hotels are cheaper',
                'optionD' => 'Traveling a few hours',
                'correct' => 'A',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 163,
                'title' => 'Why include a cancellation policy check?',
                'optionA' => 'To impress airline',
                'optionB' => 'To protect clients from losing money if plans change',
                'optionC' => 'For design purposes',
                'optionD' => 'For marketing',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 163,
                'title' => 'Which platform helps organize travel documents collaboratively?',
                'optionA' => 'Instagram',
                'optionB' => 'Google Docs',
                'optionC' => 'Pinterest',
                'optionD' => 'CapCut',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 163,
                'title' => 'A professional itinerary should NOT include:',
                'optionA' => 'Flight and hotel info',
                'optionB' => 'Backup contacts',
                'optionC' => 'Personal gossip',
                'optionD' => 'Transport plans',
                'correct' => 'C',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 163,
                'title' => 'Lead generation means:',
                'optionA' => 'Finding potential customers for a business',
                'optionB' => 'Designing posters',
                'optionC' => 'Buying ads',
                'optionD' => 'Writing reports',
                'correct' => 'A',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 163,
                'title' => 'Which type of lead has shown interest but not purchased?',
                'optionA' => 'Cold',
                'optionB' => 'Warm',
                'optionC' => 'Hot',
                'optionD' => 'Closed',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 163,
                'title' => 'Which CRM is beginner-friendly?',
                'optionA' => 'Salesforce',
                'optionB' => 'Zoho',
                'optionC' => 'HubSpot',
                'optionD' => 'Asana',
                'correct' => 'C',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 163,
                'title' => 'A CRM mainly helps a VA to:',
                'optionA' => 'Play videos',
                'optionB' => 'Track and manage client interactions',
                'optionC' => 'Edit photos',
                'optionD' => 'Record podcasts',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],

            // =========================
            // MODULE 5 — ID: 173
            // =========================
            [
                'module_id' => 173,
                'title' => 'Why do businesses rely on data?',
                'optionA' => 'It looks professional',
                'optionB' => 'It helps them make informed decisions',
                'optionC' => 'It replaces marketing',
                'optionD' => 'It’s optional',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 173,
                'title' => 'Sorting data means:',
                'optionA' => 'Deleting information',
                'optionB' => 'Rearranging information in order',
                'optionC' => 'Coloring rows',
                'optionD' => 'Printing pages',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 173,
                'title' => 'Filtering data means:',
                'optionA' => 'Showing only needed information',
                'optionB' => 'Erasing duplicates',
                'optionC' => 'Changing fonts',
                'optionD' => 'Locking cells',
                'correct' => 'A',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 173,
                'title' => 'Which formula adds numbers in Excel?',
                'optionA' => '=ADD()',
                'optionB' => '=SUM()',
                'optionC' => '=PLUS()',
                'optionD' => '=TOTAL()',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 173,
                'title' => 'Why is formatting important?',
                'optionA' => 'Makes sheets visually clear and readable',
                'optionB' => 'Decorates with colors only',
                'optionC' => 'Creates errors',
                'optionD' => 'Hides columns',
                'correct' => 'A',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 173,
                'title' => 'Pivot tables are used to:',
                'optionA' => 'Summarize and analyze large datasets',
                'optionB' => 'Draw pictures',
                'optionC' => 'Write emails',
                'optionD' => 'Check spelling',
                'correct' => 'A',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 173,
                'title' => 'In data entry, the most important value is:',
                'optionA' => 'Speed',
                'optionB' => 'Accuracy',
                'optionC' => 'Length',
                'optionD' => 'Fonts',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 173,
                'title' => 'Why is consistency necessary?',
                'optionA' => 'To keep data uniform and easier to process',
                'optionB' => 'To fill more pages',
                'optionC' => 'To impress teachers',
                'optionD' => 'To change colors often',
                'correct' => 'A',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 173,
                'title' => 'What does 2-Factor Authentication do?',
                'optionA' => 'Adds extra security to logins',
                'optionB' => 'Improves typing speed',
                'optionC' => 'Formats spreadsheets',
                'optionD' => 'Uploads pictures',
                'correct' => 'A',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 173,
                'title' => 'Why should VAs avoid sharing client files on WhatsApp?',
                'optionA' => 'It’s not secure or professional',
                'optionB' => 'It’s faster',
                'optionC' => 'It’s mandatory',
                'optionD' => 'It saves space',
                'correct' => 'A',
                'created_at' => $now,
                'updated_at' => $now
            ],

            // =========================
            // MODULE 6 — ID: 174
            // =========================
            [
                'module_id' => 174,
                'title' => 'Social media helps businesses by:',
                'optionA' => 'Entertaining only',
                'optionB' => 'Building visibility, trust, and sales',
                'optionC' => 'Replacing customer service',
                'optionD' => 'Deleting reviews',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 174,
                'title' => 'The FIRST stage in the SMM workflow is:',
                'optionA' => 'Analysis',
                'optionB' => 'Planning',
                'optionC' => 'Research',
                'optionD' => 'Posting',
                'correct' => 'C',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 174,
                'title' => 'A content calendar helps to:',
                'optionA' => 'Post randomly',
                'optionB' => 'Plan and stay consistent',
                'optionC' => 'Hide posts',
                'optionD' => 'Increase prices',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 174,
                'title' => 'What does CTA stand for?',
                'optionA' => 'Create the Ad',
                'optionB' => 'Call to Action',
                'optionC' => 'Click to Analyze',
                'optionD' => 'Caption the Article',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 174,
                'title' => 'A good caption should begin with a:',
                'optionA' => 'Hook',
                'optionB' => 'Hashtag',
                'optionC' => 'Number',
                'optionD' => 'Photo',
                'correct' => 'A',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 174,
                'title' => 'Why is relevance key in content creation?',
                'optionA' => 'It connects to audience needs and increases engagement',
                'optionB' => 'It decorates posts',
                'optionC' => 'It fills space',
                'optionD' => 'It adds color',
                'correct' => 'A',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 174,
                'title' => 'Reach measures:',
                'optionA' => 'How many people saw a post',
                'optionB' => 'How many liked it',
                'optionC' => 'How many bought something',
                'optionD' => 'How many followed',
                'correct' => 'A',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 174,
                'title' => 'Engagement measures:',
                'optionA' => 'Views only',
                'optionB' => 'Likes, comments, shares, saves',
                'optionC' => 'Profile visits',
                'optionD' => 'Messages sent',
                'correct' => 'B',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 174,
                'title' => 'Why are saves and shares important?',
                'optionA' => 'They show deep audience interest and content value',
                'optionB' => 'They delete the post',
                'optionC' => 'They reduce reach',
                'optionD' => 'They add captions',
                'correct' => 'A',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'module_id' => 174,
                'title' => 'Monthly social-media reporting is important because:',
                'optionA' => 'It proves results and guides future strategy',
                'optionB' => 'It’s required by Instagram',
                'optionC' => 'It hides weak posts',
                'optionD' => 'It replaces captions',
                'correct' => 'A',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ];

        DB::table('questions')->upsert(
            $data,
            ['title'], // Unique key to check duplicates
            [
                'module_id',
                'optionA',
                'optionB',
                'optionC',
                'optionD',
                'correct',
                'updated_at'
            ]
        );
    }
}
