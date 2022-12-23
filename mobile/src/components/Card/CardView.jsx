import {Avatar, Card, Title, Paragraph, Button} from "react-native-paper";
import React from "react";

const LeftContent = props => <Avatar.Icon {...props} icon="folder" />
export const CardView = () => {
  return (
    <Card>
      <Card.Title title="Card Title" subtitle="Card Subtitle" left={LeftContent}/>
      <Card.Content>
        <Title>Card title</Title>
        <Paragraph>Card content</Paragraph>
      </Card.Content>
      <Card.Cover source={{uri: 'https://picsum.photos/700'}}/>
      <Card.Actions>
        <Button>Cancel</Button>
        <Button>Ok</Button>
      </Card.Actions>
    </Card>
  )
}
