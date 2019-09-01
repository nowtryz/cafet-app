function push_image_to_gitlab() {
    local image="$1" sha="$2" tag="$3"
    docker pull $image:$sha
    docker tag $image:$sha $image:$tag
    docker push $image:$tag
}

function tag_images() {
    local tag=$1
    push_image_to_gitlab $DATABASE_IMAGE $IMAGE_ID $tag
    push_image_to_gitlab $SERVER_IMAGE $IMAGE_ID $tag
}