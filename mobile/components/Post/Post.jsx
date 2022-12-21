import styled from "styled-components/native";
import React from "react";

const PostItem = styled.View`
  padding: 15px;
  background-color: pink;
  flex: 1;
  flex-direction: row;
  align-items: center;
  margin-bottom: 15px;
`;
const PostImage = styled.Image`
  height: 100px;
  width: 100px;
  object-fit: cover;
`;
const PostTitle = styled.Text`
  font-size: 16px;
  font-weight: 700;
  color: black;
`;

export const Post = ({title, imageUrl}) => {
  return (
    <PostItem>

{/*
      <Image source={{uri: 'https://yandex.ru/images/search?text=%D0%A1%D0%BE%D0%B1%D0%B0%D0%BA%D0%B0%20%D0%9A%D0%BE%D1%80%D0%B3%D0%B8&nl=1&source=morda&pos=11&rpt=simage&img_url=http%3A%2F%2Foir.mobi%2Fuploads%2Fposts%2F2021-05%2F1620341049_26-oir_mobi-p-korgi-sobaka-pembrok-zhivotnie-krasivo-fot-29.jpg&lr=213'}} width={100} height={100}/>
*/}

      {/*<PostImage
        source={{uri: imageUrl}} height={100} width={100}
      />*/}

      <PostTitle>{title}</PostTitle>
    </PostItem>
  )
}
