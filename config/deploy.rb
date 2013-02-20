set :application, "signin-web"
set :repository,  "https://github.com/theodi/signin-web.git"

set :user, 'deploy'
set :deploy_to, "/var/www/signin.office.theodi.org"
set :use_sudo, false

set :normalize_asset_timestamps, false

role :web, "signin.office.theodi.org"
role :app, "signin.office.theodi.org"
role :db,  "signin.office.theodi.org", :primary => true

after "deploy:update_code", "deploy:link_config"

namespace :deploy do
  task :link_config do
    run "ln -nfs #{shared_path}/config/database_connector.php #{release_path}/database_connector.php"
    run "ln -nfs #{shared_path}/data/staff.csv #{release_path}/staff/staff.csv"
    run "ln -nfs #{shared_path}/data/stock #{release_path}/staff/stock"
    run "ln -nfs #{shared_path}/data/staff.csv #{release_path}/staff.csv"
    run "ln -nfs #{shared_path}/data/stock #{release_path}/stock"
    run "ln -nfs #{shared_path}/data/staff.csv #{release_path}/individual/staff.csv"
    run "ln -nfs #{shared_path}/data/stock #{release_path}/individual/stock"
  end  
end

namespace :staff do
  task :update do
    run "cd #{current_path}/staff/; php update_staff.php"
  end
end

after "deploy:restart", "deploy:cleanup", "staff:update"