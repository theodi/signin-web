set :application, "signin-web"
set :repository,  "https://github.com/theodi/signin-web.git"

set :user, 'deploy'
set :deploy_to, "/var/www/signin.office.theodi.org"
set :use_sudo, false

set :normalize_asset_timestamps, false

role :web, "signin.office.theodi.org"
role :app, "signin.office.theodi.org"
role :db,  "signin.office.theodi.org", :primary => true

after "deploy:update_code", "deploy:setup_card_file", "deploy:link_config"

namespace :deploy do
  task :link_config do
    run "ln -nfs #{shared_path}/config/eventbrite_api_key.php #{release_path}/stats/eventbrite/eventbrite_api_key.php"
    run "ln -nfs #{shared_path}/config/database_connector.php #{release_path}/database_connector.php"
    run "ln -nfs #{shared_path}/data/staff.csv #{release_path}/staff/staff.csv"
    run "ln -nfs #{shared_path}/data/stock #{release_path}/staff/stock"
    run "ln -nfs #{shared_path}/data/staff.csv #{release_path}/staff.csv"
    run "ln -nfs #{shared_path}/data/stock #{release_path}/stock"
    run "ln -nfs #{shared_path}/keycard.txt #{release_path}/keycard.txt"
  end  
  task :setup_card_file do
    run "touch #{shared_path}/keycard.txt"
    run "chmod 777 #{shared_path}/keycard.txt"
  end  
end

namespace :staff do
  task :update do
    run "cd #{current_path}/staff/; php update_staff.php"
  end
end

after "deploy:restart", "deploy:cleanup", "staff:update"
