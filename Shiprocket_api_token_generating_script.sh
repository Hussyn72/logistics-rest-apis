#!/bin/bash

# Generate Shiprocket authentication token
SHIPROCKET_API_KEY="husain@staff.ownmail.com"
SHIPROCKET_API_SECRET="Warmc0nnect@"
AUTH_TOKEN=$(curl -s -X POST https://apiv2.shiprocket.in/v1/external/auth/login -H "content-type: application/json" -d "{\"email\":\"$SHIPROCKET_API_KEY\",\"password\":\"$SHIPROCKET_API_SECRET\"}" | jq -r '.token')

# Update authentication token value in database
/usr/local/pgsql/bin/psql e2fax -e -c "UPDATE config_values SET value='$AUTH_TOKEN' WHERE name='simmis' AND key='Shiprocket_API_Token'"

echo "Shiprocket authentication token has been generated and updated in database."

