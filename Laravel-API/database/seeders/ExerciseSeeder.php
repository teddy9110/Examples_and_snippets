<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use Rhf\Modules\Exercise\Models\ExerciseCategory;
use Rhf\Modules\Exercise\Models\ExerciseLevel;
use Rhf\Modules\Exercise\Models\ExerciseLocation;
use Rhf\Modules\Exercise\Models\ExerciseFrequency;
use Rhf\Modules\Exercise\Models\Exercise;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sortOrder = 0;

        // Create the exercise levels
        $athletic = ExerciseLevel::where('title', '=', 'Athletic')->first();
        if (!$athletic) {
            $athletic = ExerciseLevel::create([
                'title' => 'Athletic',
                'slug' => 'athletic',
            ]);
        }
        $standard = ExerciseLevel::where('title', '=', 'Standard')->first();
        if (!$standard) {
            $standard = ExerciseLevel::create([
                'title' => 'Standard',
                'slug' => 'standard',
            ]);
        }

        // Retrieve the locations and the frequencies
        $gym = ExerciseLocation::where('title', '=', 'Gym')->first();
        $home = ExerciseLocation::where('title', '=', 'Home')->first();
        $frequency3 = ExerciseFrequency::where('amount', '=', 3)->first();
        $frequency6 = ExerciseFrequency::where('amount', '=', 6)->first();

        // Create the exercise categories with associated exercises
        /*
        * REST
        */
        $category = ExerciseCategory::create([
            'title' => 'Rest',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Rest',
            'quantity'              => '',
            'content'               => 'Rest day.',
            'video'                 => '',
            'descriptive_title'     => '',
        ]);

        /*
        * HOME
        */
        $category = ExerciseCategory::create([
            'title' => 'Full Body 1',
            'facebook_id' => 711095852609694,
            'descriptive_title'     => '',
        ]);
        $category->exercise_location_id = $home->id;
        $category->exercise_level_id = $athletic->id;
        $category->exerciseFrequencies()->attach([$frequency3->id]);
        $category->exerciseFrequencies()->attach([$frequency3->id]);
        $category->exerciseFrequencies()->attach([$frequency3->id]);
        $category->save();

        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Squat',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'With the barbell on your upper back, stand with both feet slightly wider than hip width.Keeping the chest up at all time, lower yourself carefully by bending the hips and knees.Keep your weight on both heels as you squat down until your upper thighs are parallel to the ground.Keeping the knees tracking straight at all time, return to the top.',
            'video'                 => '212843126322824',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Shoulder Press',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Standing tall, hold the barbell in your hands at shoulder level.Keep your back straight and your chest up at all time.Press the barbell straight up until your arms are straight and the elbows are in line with your ears.Lower the barbell back to shoulder level.Repeat.',
            'video'                 => '1485795981550580',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Bent over rows',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Grasp bar with hands shoulder width apart.Push your hips back and bend forward until your upper body is parallel to the ground, keeping your back straight.Lower the bar down until your arms are straight, keeping the bar close to your legs.Pull the bar into your stomach, and repeat movement.',
            'video'                 => '376068336284775',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Push Ups',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'In a pushup position, balance on your hands and feet, with feet seperated wide.Keep your head, neck, and body aligned in a straight.Lower yourself as far as you can by bending your arms.Push back up to the top.',
            'video'                 => '2003380596418777',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Barbell Curl',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Standing with your body stabilized, hold the barbell down in front of you, palms forward.Keep your chest up and your elbows braced at your sides at all time.Bend your elbows and pull the barbell up toward the shoulders until your elbows can\'t bend any more.Return slowly to the bottom position.',
            'video'                 => '129624474642290/',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Sumo Dead Lift',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Grab the bar with your hands at hip-width, and set up with your back straight and your chest out, and legs wide. Keeping your chest up and your back straight at all time, carefully lift the bar off the ground to a standing position.Slowly return to the start position.',
            'video'                 => '776931672653604',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Lunges',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'With the barbell on your upper back, step one foot forward about 18-24 inches.Immediately bend the knees and descend onto the front leg, allowing the back knee to come close to the ground.Keep the weight on the front heel and maintain a straight torso.Push back up with the front heel and return to the standing position.',
            'video'                 => '233126607433646',
            'descriptive_title'     => '',
        ]);

        $category = ExerciseCategory::create([
            'title' => 'Full Body 2',
            'facebook_id' => 2279448192067353,
            'descriptive_title'     => '',
        ]);
        $category->exercise_location_id = $home->id;
        $category->exercise_level_id = $standard->id;
        $category->exerciseFrequencies()->attach([$frequency3->id]);
        $category->exerciseFrequencies()->attach([$frequency3->id]);
        $category->exerciseFrequencies()->attach([$frequency3->id]);
        $category->save();

        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'TRX Squat',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'With your feet wider than shoulder width, hold onto the TRX rope.',
            'video'                 => '212843126322824',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Shoulder Press',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Sit on a bench with the chest upright and spine straight.Grasp the bar slightly wider than shoulder width using an overhand grip.Raise the bar overhead by extending the arms.Lower the bar under control. Repeat.',
            'video'                 => '1485795981550580',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Bent over rows',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Grasp bar with hands shoulder width apart.Push your hips back and bend forward until your upper body is parallel to the ground, keeping your back straight.Lower the bar down until your arms are straight, keeping the bar close to your legs.Pull the bar into your stomach, and repeat movement.',
            'video'                 => '376068336284775',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Knee Push Up',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'In a pushup position, balance on your hands and knees.Keep your head, neck, and body aligned in a straight line.Lower yourself as far as you can by bending your arms.Push back up to the top.',
            'video'                 => '2003380596418777',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Barbell Curl',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Standing with your body stabilized, hold the barbell down in front of you, palms forward.Keep your chest up and your elbows braced at your sides at all time.Bend your elbows and pull the barbell up toward the shoulders until your elbows can\'t bend any more.Return slowly to the bottom position.',
            'video'                 => '129624474642290',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'TRX sumo squat',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'With your feet placed outside of hip width (wide), hold onto the TRX Rope.Slowly descend by bending your hips and knees.Keep your back straight and your chest up, and maintain the weight on your heels. Keeping the knees in line with the angle of the feet, return to the top.',
            'video'                 => '2173841266005840',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'TRX lunges',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Facing forward with your torso straight, step one foot backward about 18 to 24 inches Whilst holding tight to the TRX rope.Immediately bend the knees and descend onto the front leg, allowing the back knee to come close to the ground.Keep the weight on the front heel and maintain a straight torso.Push back up with the back foot and return to the standing position.',
            'video'                 => '296572887870559',
            'descriptive_title'     => '',
        ]);

        $category = ExerciseCategory::create([
            'title' => 'Push 1',
            'facebook_id' => 219928295606333,
            'descriptive_title'     => '',
        ]);
        $category->exercise_location_id = $home->id;
        $category->exercise_level_id = $athletic->id;
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->save();

        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Shoulder Press',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Standing tall, hold the barbell in your hands at shoulder level.Keep your back straight and your chest up at all time.Press the barbell straight up until your arms are straight and the elbows are in line with your ears.Lower the barbell back to shoulder level. Repeat.',
            'video'                 => '1485795981550580',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Push Ups',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'In a pushup position, balance on your hands and feet, with feet seperated wide.Keep your head, neck, and body aligned in a straight. Lower yourself as far as you can by bending your arms.Push back up to the top.',
            'video'                 => '2003380596418777',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Bench Dips',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Balance between two benches, with your feet on one bench and your hands on the other. Keep you chest up and your back straight at all time. Lower yourself until your elbows are bent to about 90 degrees. Then press back up to straight arms.',
            'video'                 => '1984645774986126',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Front Raises',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Grasp bar with hands shoulder width apart, and arms down at sides. Slowly raise bar straight forward and up toward shoulders; return to start position and repeat.',
            'video'                 => '312020376080746',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Lateral Raises',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Standing tall, hold the dumbbells in your hands with your arms hanging straight.Keeping your arms straight, lift the dumbbells to the sides. Stop at shoulder height and return the dumbbells to the start position.',
            'video'                 => '522683924880254',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Overhead Triceps Extension',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Sit on a bench with the chest upright and spine straight.Hold the barbell overhead.Lower the barbell toward the back by bending the elbow. Extend the barbell overhead by contracting the triceps.Repeat.',
            'video'                 => '1059122047601330',
            'descriptive_title'     => '',
        ]);

        $category = ExerciseCategory::create([
            'title' => 'Pull 1',
            'facebook_id' => 723303034709993,
            'descriptive_title'     => '',
        ]);
        $category->exercise_location_id = $home->id;
        $category->exercise_level_id = $athletic->id;
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->save();

        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Bent Over Rows',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Grasp bar with hands shoulder width apart.Push your hips back and bend forward until your upper body is parallel to the ground, keeping your back straight.Lower the bar down until your arms are straight, keeping the bar close to your legs.Pull the bar into your stomach, and repeat movement.',
            'video'                 => '376068336284775',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Rear Delt Flys',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => '"Set doors wide, adjust swivel pulleys midway between waist and chest level and set resistance.Stand facing into machine an arms length out from the pulleys; grasp opposite handles with a vertical grip and elbows slightly bent.Slowly pull the handles out horizontally in an arcing motion until they are even with shoulders.Slowly return to start position and repeat."',
            'video'                 => '213977392814444',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Upright Rows',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Standing, hold the barbell in front of your thighs, arms straight and palms facing back.Lift the barbell straight up by leading up with your elbows.Keep the barbell close to your body on the way up, and relax your wrists by letting them bend.Lower the barbell back to the front of your thighs.',
            'video'                 => '212875762953635',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'TRX Row',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Standing and leaning back whilst bracing your feet firmly, hold the TRX rope, palm down. Use the angle of your body to dictate resistance. Pull the TRX Rope straight back toward your chest, keeping the elbows out.Keep your chest up and your trunk stabilized. Slowly straighten your arm to the start position.',
            'video'                 => '2231224000454535',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Barbell Curl',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Standing with your body stabilized, hold the barbell down in front of you, palms forward.Keep your chest up and your elbows braced at your sides at all time.Bend your elbows and pull the barbell up toward the shoulders until your elbows can\'t bend any more. Return slowly to the bottom position.',
            'video'                 => '129624474642290',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'TRX Lat Pull',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Whilst leaning back on your knees, Hold the TRX Rope toward its ends, with just enough tension when the arms are straight up.Pull the TRX Rope to the sides, keeping the elbows out, And lifting yourself away from the floor to an upright position. Pause when the elbows reach your sides and the hands near head level.Return slowly to the top, with your arms straight again.',
            'video'                 => '265530274143285',
            'descriptive_title'     => '',
        ]);

        $category = ExerciseCategory::create([
            'title' => 'Legs 1',
            'facebook_id' => 358866961335207,
            'descriptive_title'     => '',
        ]);
        $category->exercise_location_id = $home->id;
        $category->exercise_level_id = $athletic->id;
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->save();

        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Lunges',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'With the barbell on your upper back, step one foot forward about 18-24 inches.Immediately bend the knees and descend onto the front leg, allowing the back knee to come close to the ground.Keep the weight on the front heel and maintain a straight torso.Push back up with the front heel and return to the standing position.',
            'video'                 => '233126607433646',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Squats',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'With the barbell on your upper back, stand with both feet slightly wider than hip width.Keeping the chest up at all time, lower yourself carefully by bending the hips and knees.Keep your weight on both heels as you squat down until your upper thighs are parallel to the ground.Keeping the knees tracking straight at all time, return to the top.',
            'video'                 => '212843126322824',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Hip Thrusts',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Lie on your back with your knees bent, feet placed flat on the ground about 12 to 14 inches from the hips. Place the bar just below your Hips. Keeping your hands out to the barto help balance, lift your hips up so that thighs and trunk form a straight line.Return slowly to the starting position.',
            'video'                 => '956669234529294',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Sumo Squats',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'With your feet placed outside of hip width (wide), hold onto the TRX Rope.Slowly descend by bending your hips and knees.Keep your back straight and your chest up, and maintain the weight on your heels.Keeping the knees in line with the angle of the feet, return to the top.',
            'video'                 => '1248155262001557',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Romanian Dead Lifts',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Grab the barbell and stand up straight with a slight bend in your knees.Keeping your back straight, push your butt back and bend forward.Keep going until your hamstrings are too tight to go any further. When you have reached the limit of your range of motion, use your hamstrings to pull you back up to standing.',
            'video'                 => '510512662791859',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'TRX Non Lock Out',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'With the feet placed directly under the hips (narrow), hold the TRX rope tight infront of you.Slowly descend by bending your hips and knees.Keep your back straight and your chest up, and maintain the weight on your heels.Keeping the knees in line with the angle of the feet, return to 3/4 of the way and repeat.',
            'video'                 => '2463656793896407',
            'descriptive_title'     => '',
        ]);

        $category = ExerciseCategory::create([
            'title' => 'Push 2',
            'facebook_id' => 390462001507278,
            'descriptive_title'     => '',
        ]);
        $category->exercise_location_id = $home->id;
        $category->exercise_level_id = $standard->id;
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->save();

        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Shoulder Press',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Sit on a bench with the chest upright and spine straight.Grasp the bar slightly wider than shoulder width using an overhand grip.Raise the bar overhead by extending the arms.Lower the bar under control. Repeat.',
            'video'                 => '1485795981550580',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Knee Push Ups',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'In a pushup position, balance on your hands and knees.Keep your head, neck, and body aligned in a straight line.Lower yourself as far as you can by bending your arms.Push back up to the top.',
            'video'                 => '2003380596418777',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Front Raises',
            'quantity'              => '6 Sets 8-12 Reps',
            'content'               => 'Grasp bar with hands shoulder width apart, and arms down at sides.Slowly raise bar straight forward and up toward shoulders; return to start position and repeat.',
            'video'                 => '312020376080746',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Lateral Raises',
            'quantity'              => '6 Sets 8-12 Reps',
            'content'               => 'Standing tall, hold the dumbbells in your hands with your arms hanging straight.Keeping your arms straight, lift the dumbbells to the sides.Stop at shoulder height and return the dumbbells to the start position.',
            'video'                 => '522683924880254',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Overhead Triceps Extension',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Sit on a bench with the chest upright and spine straight.Hold the barbell overhead.Lower the barbell toward the back by bending the elbow.Extend the barbell overhead by contracting the triceps. Repeat.',
            'video'                 => '1059122047601330',
            'descriptive_title'     => '',
        ]);

        $category = ExerciseCategory::create([
            'title' => 'Pull 2',
            'facebook_id' => 228582988027138,
            'descriptive_title'     => '',
        ]);
        $category->exercise_location_id = $home->id;
        $category->exercise_level_id = $standard->id;
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->save();

        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Bent Over Rows',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Grasp bar with hands shoulder width apart.Push your hips back and bend forward until your upper body is parallel to the ground, keeping your back straight.Lower the bar down until your arms are straight, keeping the bar close to your legs.Pull the bar into your stomach, and repeat movement.',
            'video'                 => '376068336284775',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Rear Delt Flys',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => '"Hold the dumbbells in your hands and bend forward while shifting your hips back.Keep your back straight and your chest up at all time.Pull the dumbbells out and up until they are level with the shoulders.Return slowly to the ground, and repeat.

"',
            'video'                 => '213977392814444',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Upright Rows',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Standing, hold the barbell in front of your thighs, arms straight and palms facing back.Lift the barbell straight up by leading up with your elbows.Keep the barbell close to your body on the way up, and relax your wrists by letting them bend. Lower the barbell back to the front of your thighs.',
            'video'                 => '212875762953635',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'TRX Row',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Standing and leaning back whilst bracing your feet firmly, hold the TRX rope, palm down. Use the angle of your body to dictate resistance. Pull the TRX Rope straight back toward your chest, keeping the elbows out.Keep your chest up and your trunk stabilized. Slowly straighten your arm to the start position.',
            'video'                 => '2231224000454535',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Barbell Row',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Standing with your body stabilized, hold the barbell down in front of you, palms forward.Keep your chest up and your elbows braced at your sides at all time.Bend your elbows and pull the barbell up toward the shoulders until your elbows can\'t bend any more. Return slowly to the bottom position.',
            'video'                 => '129624474642290',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'TRX Lat Pull',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Whilst leaning back on your knees, Hold the TRX Rope toward its ends, with just enough tension when the arms are straight up.Pull the TRX Rope to the sides, keeping the elbows out, And lifting yourself away from the floor to an upright position. Pause when the elbows reach your sides and the hands near head level. Return slowly to the top, with your arms straight again.',
            'video'                 => '265530274143285',
            'descriptive_title'     => '',
        ]);

        $category = ExerciseCategory::create([
            'title' => 'Legs 2',
            'facebook_id' => 646431632478515,
            'descriptive_title'     => '',
        ]);
        $category->exercise_location_id = $home->id;
        $category->exercise_level_id = $standard->id;
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->save();

        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'TRX Lunges',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Facing forward with your torso straight, step one foot backward about 18 to 24 inches Whilst holding tight to the TRX rope.Immediately bend the knees and descend onto the front leg, allowing the back knee to come close to the ground.Keep the weight on the front heel and maintain a straight torso. Push back up with the back foot and return to the standing position.',
            'video'                 => '296572887870559',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'TRX Squats',
            'quantity'              => '6 Sets 8-12 Reps',
            'content'               => 'With your feet wider than shoulder width, hold onto the TRX rope.',
            'video'                 => '536569210157866',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'TRX Sumo Squat',
            'quantity'              => '6 Sets 8-12 Reps',
            'content'               => 'With your feet placed outside of hip width (wide), hold onto the TRX Rope.Slowly descend by bending your hips and knees.Keep your back straight and your chest up, and maintain the weight on your heels.Keeping the knees in line with the angle of the feet, return to the top.',
            'video'                 => '2173841266005840',
            'descriptive_title'     => '',
        ]);

        /*
        * Gym
        */
        $category = ExerciseCategory::create([
            'title' => 'Full Body 1',
            'facebook_id' => 2475431792471097,
            'descriptive_title'     => '',
        ]);
        $category->exercise_location_id = $gym->id;
        $category->exercise_level_id = $athletic->id;
        $category->exerciseFrequencies()->attach([$frequency3->id]);
        $category->exerciseFrequencies()->attach([$frequency3->id]);
        $category->exerciseFrequencies()->attach([$frequency3->id]);
        $category->save();

        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'TRX Squat',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'With your feet wider than shoulder width, hold onto the TRX rope',
            'video'                 => '536569210157866',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Shoulder press',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Standing tall, hold the barbell in your hands at shoulder level.Keep your back straight and your chest up at all time.Press the barbell straight up until your arms are straight and the elbows are in line with your ears.Lower the barbell back to shoulder level.Repeat.',
            'video'                 => '1485795981550580',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Knee Push up',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'In a pushup position, balance on your hands and knees.Keep your head, neck, and body aligned in a straight line.Lower yourself as far as you can by bending your arms.Push back up to the top.',
            'video'                 => '2003380596418777',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Bench Dips',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Balance between two benches, with your feet on one bench and your hands on the other. Keep you chest up and your back straight at all time.Lower yourself until your elbows are bent to about 90 degrees. Then press back up to straight arms.',
            'video'                 => '1984645774986126',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Low Back Row',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Sit on the machine and hold the handles.Pull the bar to the waist.Imagine the weight in against your elbows and squeeze the back muscles.Avoid rounding the shoulders. Return the weight. Repeat.',
            'video'                 => '2288572434713202',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Lat Pull Down',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Start with your hands on the bar slightly wider than shoulder-width, arms straight and palms facing forward.Pull the bar straight down under your chin, toward the top of your chest. Keep your chest up and your elbows out to the side. Return slowly to the top, with your arms straight again.',
            'video'                 => '772841929717476',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Barbell Curl',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Standing with your body stabilized, hold the barbell down in front of you, palms forward. Keep your chest up and your elbows braced at your sides at all time. Bend your elbows and pull the barbell up toward the shoulders until your elbows can\'t bend any more. Return slowly to the bottom position.',
            'video'                 => '129624474642290',
            'descriptive_title'     => '',
        ]);

        $category = ExerciseCategory::create([
            'title' => 'Full Body 1',
            'facebook_id' => 2475431792471097,
            'descriptive_title'     => '',
        ]);
        $category->exercise_location_id = $gym->id;
        $category->exercise_level_id = $standard->id;
        $category->exerciseFrequencies()->attach([$frequency3->id]);
        $category->exerciseFrequencies()->attach([$frequency3->id]);
        $category->exerciseFrequencies()->attach([$frequency3->id]);
        $category->save();

        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'TRX Squat',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'With your feet wider than shoulder width, hold onto the TRX rope',
            'video'                 => '536569210157866',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Shoulder press',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Standing tall, hold the barbell in your hands at shoulder level.Keep your back straight and your chest up at all time.Press the barbell straight up until your arms are straight and the elbows are in line with your ears.Lower the barbell back to shoulder level.Repeat.',
            'video'                 => '1485795981550580',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Knee Push up',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'In a pushup position, balance on your hands and knees.Keep your head, neck, and body aligned in a straight line.Lower yourself as far as you can by bending your arms.Push back up to the top.',
            'video'                 => '2003380596418777',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Bench Dips',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Balance between two benches, with your feet on one bench and your hands on the other. Keep you chest up and your back straight at all time.Lower yourself until your elbows are bent to about 90 degrees. Then press back up to straight arms.',
            'video'                 => '1984645774986126',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Low Back Row',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Sit on the machine and hold the handles.Pull the bar to the waist.Imagine the weight in against your elbows and squeeze the back muscles.Avoid rounding the shoulders. Return the weight. Repeat.',
            'video'                 => '2288572434713202',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Lat Pull Down',
            'quantity'              => '4 Sets 8-12 Reps',
            'content'               => 'Start with your hands on the bar slightly wider than shoulder-width, arms straight and palms facing forward.Pull the bar straight down under your chin, toward the top of your chest. Keep your chest up and your elbows out to the side. Return slowly to the top, with your arms straight again.',
            'video'                 => '772841929717476',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Barbell Curl',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Standing with your body stabilized, hold the barbell down in front of you, palms forward. Keep your chest up and your elbows braced at your sides at all time. Bend your elbows and pull the barbell up toward the shoulders until your elbows can\'t bend any more. Return slowly to the bottom position.',
            'video'                 => '129624474642290',
            'descriptive_title'     => '',
        ]);

        $category = ExerciseCategory::create([
            'title' => 'Push 1',
            'facebook_id' => 776933642639121,
            'descriptive_title'     => '',
        ]);
        $category->exercise_location_id = $gym->id;
        $category->exercise_level_id = $athletic->id;
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->save();

        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Dumbbell Shoulder Press',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Seated on a folding bench with its back-rest propped straight up, lean your back against the back-rest.Keeping your chest up, hold the dumbbells at shoulder level, palms forward.Press the dumbbells straight up until your arms are straight and the elbows are in line with your ears.Lower the dumbbells back to shoulder level.',
            'video'                 => '210530006518616',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Dumbbell Chest Press',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Lying on a bench with your feet firmly on the ground, hold the dumbbells directly over the chest, arms straight.Keep your chest up at all time, and allow the lower back to maintain a natural arch (not excessive). Lower the dumbbells to the sides until they are level with the top of the chest. Press them back up to straight arms, preventing them from touching at the top.',
            'video'                 => '231129854448306',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Bench Dips',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Balance between two benches, with your feet on one bench and your hands on the other. Keep you chest up and your back straight at all time.Lower yourself until your elbows are bent to about 90 degrees. Then press back up to straight arms.',
            'video'                 => '1984645774986126',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Dumbbell Chest Flys',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'With the dumbbells in you hand, lay down flat on the bench.Your head, neck, upper back and hips should make contact with the bench.Start with the dumbbells up, arm straight.Slowly lower the dumbbells to the side, keeping your arms slightly bent.Pause, and return the dumbbells to the top. Repeat.',
            'video'                 => '346898462796005',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Front Raises',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Grasp bar with hands shoulder width apart, and arms down at sides.Slowly raise bar straight forward and up toward shoulders; return to start position and repeat.',
            'video'                 => '312020376080746',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Lateral Raises',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Standing tall, hold the dumbbells in your hands with your arms hanging straight.Keeping your arms straight, lift the dumbbells to the sides. Stop at shoulder height and return the dumbbells to the start position.',
            'video'                 => '522683924880254',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Tricep Extension',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Stand in front of a cable column. Hold onto the ends of the attached rope handles.Keeping your chest up and your elbows braced against your sides, extend the elbows until your arms are straight. Return slowly to the starting position.',
            'video'                 => '773974279627324',
            'descriptive_title'     => '',
        ]);

        $category = ExerciseCategory::create([
            'title' => 'Pull 1',
            'facebook_id' => 2244876522499717,
            'descriptive_title'     => '',
        ]);
        $category->exercise_location_id = $gym->id;
        $category->exercise_level_id = $athletic->id;
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->save();

        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Lat Pull Down',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Start with your hands on the bar slightly wider than shoulder-width, arms straight and palms facing forward.Pull the bar straight down under your chin, toward the top of your chest.Keep your chest up and your elbows out to the side. Return slowly to the top, with your arms straight again.',
            'video'                 => '772841929717476',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Bent Over Rows',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Grasp bar with hands shoulder width apart.Push your hips back and bend forward until your upper body is parallel to the ground, keeping your back straight.Lower the bar down until your arms are straight, keeping the bar close to your legs. Pull the bar into your stomach, and repeat movement.',
            'video'                 => '376068336284775',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Dumbbell Rows',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Grab a dumbbell in one hand and support the opposite side of your body on a bench so your torso is parallel to the ground.Start with your arm extended and pull the dumbbell up to your body.Lower the weight back to the starting position.',
            'video'                 => '341433886453707',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Lat Pushdowns',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Maintain a slight bend in your elbows keeping your wrists straight throughout.Have a slight bend in legs with an accute arch in your back. Press the bar down to your thighs, then raising bar back to chin level. Keep everything tight and really focus on your lats.',
            'video'                 => '356468834913782',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Rear Delt Flys',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Set doors wide, adjust swivel pulleys midway between waist and chest level and set resistance.Stand facing into machine an arms length out from the pulleys; grasp opposite handles with a vertical grip and elbows slightly bent.Slowly pull the handles out horizontally in an arcing motion until they are even with shoulders.Slowly return to start position and repeat.',
            'video'                 => '592823294473453',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Barbell Curls',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Standing with your body stabilized, hold the barbell down in front of you, palms forward. Keep your chest up and your elbows braced at your sides at all time.Bend your elbows and pull the barbell up toward the shoulders until your elbows can\'t bend any more. Return slowly to the bottom position.',
            'video'                 => '129624474642290',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Hammer Curls',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Standing with your body stabilized, hold the dumbbells down at your sides, palms facing your legs. Keep your chest up and your elbows braced at your sides at all time.Bend your elbows and pull the dumbbells up toward the shoulders until your elbows can\'t bend any more. Return slowly to the bottom position.',
            'video'                 => '358679854714934',
            'descriptive_title'     => '',
        ]);

        $category = ExerciseCategory::create([
            'title' => 'Legs 1',
            'facebook_id' => 973761036147097,
            'descriptive_title'     => '',
        ]);
        $category->exercise_location_id = $gym->id;
        $category->exercise_level_id = $athletic->id;
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->save();

        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Lunges',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'With the barbell on your upper back, step one foot forward about 18-24 inches.Immediately bend the knees and descend onto the front leg, allowing the back knee to come close to the ground.Keep the weight on the front heel and maintain a straight torso. Push back up with the front heel and return to the standing position.',
            'video'                 => '233126607433646',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Hip Thrusts',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Lie on your back with your knees bent, feet placed flat on the ground about 12 to 14 inches from the hips. Place the bar just below your Hips. Keeping your hands out to the bar to help balance, lift your hips up so that thighs and trunk form a straight line.Return slowly to the starting position.',
            'video'                 => '956669234529294',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Barbell Squats',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'With the barbell on your upper back, stand with both feet slightly wider than hip width.Keeping the chest up at all time, lower yourself carefully by bending the hips and knees.Keep your weight on both heels as you squat down until your upper thighs are parallel to the ground.Keeping the knees tracking straight at all time, return to the top.',
            'video'                 => '212843126322824',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Sumo Dead Lift',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Grab the bar with your hands at hip-width, and set up with your back straight and your chest out, and legs wide. Keeping your chest up and your back straight at all time, carefully lift the bar off the ground to a standing position.Slowly return to the start position.',
            'video'                 => '776931672653604',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Romanian Dead Lift',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Grab the barbell and stand up straight with a slight bend in your knees.Keeping your back straight, push your butt back and bend forward. Keep going until your hamstrings are too tight to go any further. When you have reached the limit of your range of motion, use your hamstrings to pull you back up to standing.',
            'video'                 => '510512662791859',
            'descriptive_title'     => '',
        ]);

        $category = ExerciseCategory::create([
            'title' => 'Push 1',
            'facebook_id' => 776933642639121,
            'descriptive_title'     => '',
        ]);
        $category->exercise_location_id = $gym->id;
        $category->exercise_level_id = $standard->id;
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->save();

        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Dumbbell Shoulder Press',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Seated on a folding bench with its back-rest propped straight up, lean your back against the back-rest.Keeping your chest up, hold the dumbbells at shoulder level, palms forward.Press the dumbbells straight up until your arms are straight and the elbows are in line with your ears.Lower the dumbbells back to shoulder level.',
            'video'                 => '210530006518616',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Dumbbell Chest Press',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Lying on a bench with your feet firmly on the ground, hold the dumbbells directly over the chest, arms straight.Keep your chest up at all time, and allow the lower back to maintain a natural arch (not excessive). Lower the dumbbells to the sides until they are level with the top of the chest. Press them back up to straight arms, preventing them from touching at the top.',
            'video'                 => '231129854448306',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Bench Dips',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Balance between two benches, with your feet on one bench and your hands on the other. Keep you chest up and your back straight at all time.Lower yourself until your elbows are bent to about 90 degrees. Then press back up to straight arms.',
            'video'                 => '1984645774986126',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Dumbbell Chest Flys',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'With the dumbbells in you hand, lay down flat on the bench.Your head, neck, upper back and hips should make contact with the bench.Start with the dumbbells up, arm straight.Slowly lower the dumbbells to the side, keeping your arms slightly bent.Pause, and return the dumbbells to the top. Repeat.',
            'video'                 => '346898462796005',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Front Raises',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Grasp bar with hands shoulder width apart, and arms down at sides.Slowly raise bar straight forward and up toward shoulders; return to start position and repeat.',
            'video'                 => '312020376080746',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Lateral Raises',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Standing tall, hold the dumbbells in your hands with your arms hanging straight.Keeping your arms straight, lift the dumbbells to the sides. Stop at shoulder height and return the dumbbells to the start position.',
            'video'                 => '522683924880254',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Tricep Extension',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Stand in front of a cable column. Hold onto the ends of the attached rope handles.Keeping your chest up and your elbows braced against your sides, extend the elbows until your arms are straight. Return slowly to the starting position.',
            'video'                 => '773974279627324',
            'descriptive_title'     => '',
        ]);

        $category = ExerciseCategory::create([
            'title' => 'Pull 1',
            'facebook_id' => 2244876522499717,
            'descriptive_title'     => '',
        ]);
        $category->exercise_location_id = $gym->id;
        $category->exercise_level_id = $standard->id;
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->save();

        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Lat Pull Down',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Start with your hands on the bar slightly wider than shoulder-width, arms straight and palms facing forward.Pull the bar straight down under your chin, toward the top of your chest.Keep your chest up and your elbows out to the side. Return slowly to the top, with your arms straight again.',
            'video'                 => '772841929717476',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Bent Over Rows',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Grasp bar with hands shoulder width apart.Push your hips back and bend forward until your upper body is parallel to the ground, keeping your back straight.Lower the bar down until your arms are straight, keeping the bar close to your legs. Pull the bar into your stomach, and repeat movement.',
            'video'                 => '376068336284775',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Dumbbell Rows',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Grab a dumbbell in one hand and support the opposite side of your body on a bench so your torso is parallel to the ground.Start with your arm extended and pull the dumbbell up to your body.Lower the weight back to the starting position.',
            'video'                 => '341433886453707',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Lat Pushdowns',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Maintain a slight bend in your elbows keeping your wrists straight throughout.Have a slight bend in legs with an accute arch in your back. Press the bar down to your thighs, then raising bar back to chin level. Keep everything tight and really focus on your lats.',
            'video'                 => '356468834913782',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Rear Delt Flys',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Set doors wide, adjust swivel pulleys midway between waist and chest level and set resistance.Stand facing into machine an arms length out from the pulleys; grasp opposite handles with a vertical grip and elbows slightly bent.Slowly pull the handles out horizontally in an arcing motion until they are even with shoulders.Slowly return to start position and repeat.',
            'video'                 => '592823294473453',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Barbell Curls',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Standing with your body stabilized, hold the barbell down in front of you, palms forward. Keep your chest up and your elbows braced at your sides at all time.Bend your elbows and pull the barbell up toward the shoulders until your elbows can\'t bend any more. Return slowly to the bottom position.',
            'video'                 => '129624474642290',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Hammer Curls',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Standing with your body stabilized, hold the dumbbells down at your sides, palms facing your legs. Keep your chest up and your elbows braced at your sides at all time.Bend your elbows and pull the dumbbells up toward the shoulders until your elbows can\'t bend any more. Return slowly to the bottom position.',
            'video'                 => '358679854714934',
            'descriptive_title'     => '',
        ]);

        $category = ExerciseCategory::create([
            'title' => 'Legs 1',
            'facebook_id' => 973761036147097,
            'descriptive_title'     => '',
        ]);
        $category->exercise_location_id = $gym->id;
        $category->exercise_level_id = $standard->id;
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->exerciseFrequencies()->attach([$frequency6->id]);
        $category->save();

        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Lunges',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'With the barbell on your upper back, step one foot forward about 18-24 inches.Immediately bend the knees and descend onto the front leg, allowing the back knee to come close to the ground.Keep the weight on the front heel and maintain a straight torso. Push back up with the front heel and return to the standing position.',
            'video'                 => '233126607433646',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Hip Thrusts',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Lie on your back with your knees bent, feet placed flat on the ground about 12 to 14 inches from the hips. Place the bar just below your Hips. Keeping your hands out to the bar to help balance, lift your hips up so that thighs and trunk form a straight line.Return slowly to the starting position.',
            'video'                 => '956669234529294',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Barbell Squats',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'With the barbell on your upper back, stand with both feet slightly wider than hip width.Keeping the chest up at all time, lower yourself carefully by bending the hips and knees.Keep your weight on both heels as you squat down until your upper thighs are parallel to the ground.Keeping the knees tracking straight at all time, return to the top.',
            'video'                 => '212843126322824',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Sumo Dead Lift',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Grab the bar with your hands at hip-width, and set up with your back straight and your chest out, and legs wide. Keeping your chest up and your back straight at all time, carefully lift the bar off the ground to a standing position.Slowly return to the start position.',
            'video'                 => '776931672653604',
            'descriptive_title'     => '',
        ]);
        Exercise::create([
            'exercise_category_id'  => $category->id,
            'sort_order'            => $sortOrder++,
            'title'                 => 'Romanian Dead Lift',
            'quantity'              => '3 Sets 8-12 Reps',
            'content'               => 'Grab the barbell and stand up straight with a slight bend in your knees.Keeping your back straight, push your butt back and bend forward. Keep going until your hamstrings are too tight to go any further. When you have reached the limit of your range of motion, use your hamstrings to pull you back up to standing.',
            'video'                 => '510512662791859',
            'descriptive_title'     => '',
        ]);

        /*
                $category = ExerciseCategory::create([
                    'title' => 'Rest',
                ]);
                for ($i = 3; $i > 0; $i--) {
                    DB::table('exercise_level_to_exercise_category')->insert([
                        'exercise_level_id' => $athletic->id,
                        'exercise_category_id' => $category->id,
                    ]);
                    DB::table('exercise_level_to_exercise_category')->insert([
                        'exercise_level_id' => $standard->id,
                        'exercise_category_id' => $category->id,
                    ]);
                    DB::table('exercise_level_to_exercise_category')->insert([
                        'exercise_level_id' => $advanced->id,
                        'exercise_category_id' => $category->id,
                    ]);
                    DB::table('exercise_level_to_exercise_category')->insert([
                        'exercise_level_id' => $expert->id,
                        'exercise_category_id' => $category->id,
                    ]);
                }

                // Create the exercises
                $exercises = ['Situps', 'Plank', 'Pushups', 'Pullups', 'Squats', 'Star Jumps'];
                $sortOrder = 0;
                foreach ($exercises as $exercise) {
                    foreach (ExerciseCategory::get() as $category) {
                        DB::table('exercise')->insert(
                            [
                                'exercise_category_id' => $category->id,
                                'sort_order'        => $sortOrder+=10,
                                'title'             => $exercise,
                                'content'           => '',
                                'video'             => '',
                            ]
                        );
                    }
                }
        */
    }
}
