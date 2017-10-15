<?php

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PageFieldsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $app_id = '1083006501758342';
        $app_secret = '73c47ebeba31ae98dc00dd7d152f2a86';
        $fb = new \Facebook\Facebook([
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'default_graph_version' => 'v2.10',

        ]);


        $access_token = $app_id . '|' . $app_secret;
        $fb->setDefaultAccessToken($access_token);
        try {

            $response = $fb->get('/1377316249228679?metadata=1');
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {

            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {

            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }


        /*UNCOMMENT THIS CODE BELOW IF YOU WANT TO PULL ALL ACTUAL FIELDS INCLUDING NON PUBLIC ONES*/
        /*WARNING? THE CODE WILL NOT WORK IF YOU DONT HAVE THE PERMISSION OF ONE OF THE FIELDS*/

        $public_fields_string = 'id,about,access_token,affiliation,app_id,app_links,artists_we_like,attire,awards' .
            ',band_interests,band_members,best_page,bio,birthday,booking_agent,built,can_checkin,can_post,category,'
            . 'category_list,checkins,company_overview,contact_address,country_page_likes,cover,culinary_team,' .
            'current_location,description,description_html,directed_by,display_subtext,displayed_message_response_time,'
            . 'emails,engagement,fan_count,featured_video,features,food_styles,founded,general_info,' .
            'general_manager,genre,global_brand_page_name,global_brand_root_id,has_added_app,has_whatsapp_number,' .
            'hometown,hours,impressum,influences,is_always_open,is_chain,is_community_page,' .
            'is_eligible_for_branded_content,is_owned,is_permanently_closed,is_published,is_unclaimed,' .
            'is_webhooks_subscribed,leadgen_has_crm_integration,leadgen_has_fat_ping_crm_integration,' .
            'leadgen_tos_acceptance_time,leadgen_tos_accepted,leadgen_tos_accepting_user,link,location,' .
            'members,merchant_review_status,messenger_ads_quick_replies_type,mission,mpg,name,' .
            'name_with_location_descriptor,network,new_like_count,offer_eligible,overall_star_rating,parent_page,' .
            'parking,payment_options,personal_info,personal_interests,pharma_safety_info,phone,place_type,plot_outline,'
            . 'press_contact,price_range,produced_by,products,promotion_ineligible_reason,' .
            'public_transit,publisher_space,rating_count,record_label,release_date,restaurant_services,' .
            'restaurant_specialties,schedule,screenplay_by,season,single_line_address,starring,start_info,' .
            'store_location_descriptor,store_number,studio,talking_about_count,unread_message_count,'
            . 'unread_notif_count,unseen_message_count,username,verification_status,voip_info,website,' .
            'were_here_count,whatsapp_number,written_by';
        $public_fields = explode(',', $public_fields_string);
        $i = 0;
        foreach ($response->getDecodedBody()['metadata']['fields'] as $field) {


            if (in_array($field['name'], $public_fields, 0)) {
                $f = new \App\PageField();
                $f->fill($field);
                $f->enabled = true;
                $f->save();
                Schema::table('pages', function (Blueprint $table) use ($field) {
                    $column_type = $this->getColumnTypeFromField($field);
                    switch ($column_type) {
                        case 'integer':
                            $table->integer($field['name'])->nullable();
                            break;
                        case 'binary':
                            $table->binary($field['name'])->nullable();
                            break;
                        case 'varchar':
                            $table->string($field['name'])->nullable();
                            break;
                        case 'longtext':
                            $table->longtext($field['name'])->nullable();
                            break;
                        case 'float':
                            $table->float($field['name'], 10, 8)->nullable();
                            break;
                        case 'boolean':
                            $table->boolean($field['name'])->nullable();
                            break;
                        default:
                            $table->text($field['name'])->nullable();
                            break;
                    }

                });
            }


        }

    }

    private function getColumnTypeFromField($field)
    {
        if (!isset($field['name'])) {
            exit('Error getting metadata from fb api');
        }
        $r = 'string';
        switch ($field['name']) {
            case 'emails':
            case 'app_links':
            case 'engagement':
            case 'location':
            case 'start_info':
            case 'voip_info':
                $r = 'binary';
                break;
        }

        if (isset($field['type'])) {
            switch ($field['type']) {
                case "bool":
                    $r = 'boolean';
                    break;
                case "float":
                    $r = "float";
                    break;
            }
        }


        return $r;
    }
}
